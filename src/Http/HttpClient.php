<?php

declare(strict_types=1);

namespace Mosend\Http;

use Mosend\Exception\ErrorFactory;
use Mosend\Exception\MosendApiException;
use Mosend\Exception\MosendNetworkException;
use Mosend\Exception\MosendValidationException;

/**
 * Cliente HTTP sobre ext-curl (cero dependencias). Resuelve la autenticación
 * (API key vs Bearer JWT), desempaqueta el envelope `{ data }`, mapea los
 * errores a excepciones tipadas y soporta reintentos opt-in e idempotencia.
 */
class HttpClient
{
    /** @var string */
    private $baseUrl;
    /** @var string|null */
    private $apiKey;
    /** @var string|null */
    private $accessToken;
    /** @var int */
    private $timeoutMs;
    /** @var string */
    private $userAgent;
    /** @var array<string,string> */
    private $defaultHeaders;
    /** @var array{max:int,on:int[],baseDelayMs?:int}|null */
    private $retries;
    /** @var RawResponse|null */
    private $lastResponse;

    /**
     * @param array<string,mixed> $config
     */
    public function __construct(array $config)
    {
        $this->baseUrl = rtrim((string) ($config['baseUrl'] ?? 'https://api.mosend.dev'), '/');
        $this->apiKey = isset($config['apiKey']) ? (string) $config['apiKey'] : null;
        $this->accessToken = isset($config['accessToken']) ? (string) $config['accessToken'] : null;
        $this->timeoutMs = (int) ($config['timeoutMs'] ?? 30000);
        $this->userAgent = (string) ($config['userAgent'] ?? 'moshipp-mosend-sdk-php/1.1.0');
        $this->defaultHeaders = $config['defaultHeaders'] ?? [];
        $this->retries = $config['retries'] ?? null;

        if (!\extension_loaded('curl')) {
            throw new MosendValidationException('La extensión ext-curl es requerida por el SDK de Mosend.');
        }
    }

    public function setAccessToken(?string $token): void
    {
        $this->accessToken = $token;
    }

    public function setApiKey(?string $key): void
    {
        $this->apiKey = $key;
    }

    public function getLastResponse(): ?RawResponse
    {
        return $this->lastResponse;
    }

    /**
     * Ejecuta una request y devuelve `data` (envelope desempaquetado).
     *
     * @param array<string,mixed> $opts query|body|multipart|headers|idempotencyKey|skipAuth
     * @return mixed
     */
    public function request(string $method, string $path, array $opts = [])
    {
        $url = $this->buildUrl($path, $opts['query'] ?? null);
        $isMultipart = isset($opts['multipart']);
        $maxAttempts = $this->retries ? max(1, ((int) $this->retries['max']) + 1) : 1;
        $lastError = null;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                [$status, $rawBody, $headers] = $this->execute($method, $url, $opts, $isMultipart);
            } catch (MosendNetworkException $e) {
                $lastError = $e;
                if ($this->retries && $attempt < $maxAttempts) {
                    usleep($this->backoffMicros($attempt));
                    continue;
                }
                throw $e;
            }

            $this->lastResponse = $this->readMeta($status, $headers);

            if ($status >= 200 && $status < 300) {
                return $this->parseEnvelope($status, $rawBody);
            }

            $apiError = $this->buildError($status, $rawBody, $headers);

            $retryable = $this->retries
                && $attempt < $maxAttempts
                && \in_array($status, $this->retries['on'], true);
            if ($retryable) {
                $lastError = $apiError;
                usleep($this->retryAfterMicros($headers, $attempt));
                continue;
            }
            throw $apiError;
        }

        if ($lastError instanceof MosendApiException || $lastError instanceof MosendNetworkException) {
            throw $lastError;
        }
        throw new MosendNetworkException('Se agotaron los reintentos.');
    }

    /**
     * @param array<string,mixed> $opts
     * @return array{0:int,1:string,2:array<string,string>}
     */
    protected function execute(string $method, string $url, array $opts, bool $isMultipart): array
    {
        $ch = curl_init();
        $headers = $this->buildHeaders($opts, $isMultipart);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, $this->timeoutMs);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($isMultipart) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->buildMultipart($opts['multipart']));
        } elseif (array_key_exists('body', $opts) && $opts['body'] !== null) {
            $json = json_encode($opts['body']);
            if ($json === false) {
                throw new MosendValidationException('No se pudo serializar el body a JSON: ' . json_last_error_msg());
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        }

        $response = curl_exec($ch);
        if ($response === false) {
            $err = curl_error($ch);
            $errno = curl_errno($ch);
            curl_close($ch);
            throw new MosendNetworkException(sprintf('curl error %d: %s', $errno, $err));
        }

        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $headerSize = (int) curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);

        $rawHeaders = substr((string) $response, 0, $headerSize);
        $body = substr((string) $response, $headerSize);

        return [$status, $body === false ? '' : $body, $this->parseHeaders($rawHeaders)];
    }

    /** @param array<string,string> $query */
    private function buildUrl(string $path, ?array $query): string
    {
        $normalized = strpos($path, '/') === 0 ? $path : '/' . $path;
        $url = $this->baseUrl . $normalized;
        if (is_array($query) && $query !== []) {
            $pairs = [];
            foreach ($query as $key => $value) {
                if ($value === null) {
                    continue;
                }
                if (is_bool($value)) {
                    $value = $value ? 'true' : 'false';
                }
                $pairs[$key] = $value;
            }
            if ($pairs !== []) {
                $url .= '?' . http_build_query($pairs);
            }
        }
        return $url;
    }

    /**
     * @param array<string,mixed> $opts
     * @return string[]
     */
    private function buildHeaders(array $opts, bool $isMultipart): array
    {
        $headers = ['Accept: application/json', 'User-Agent: ' . $this->userAgent];
        foreach ($this->defaultHeaders as $k => $v) {
            $headers[] = $k . ': ' . $v;
        }
        if (empty($opts['skipAuth'])) {
            foreach ($this->authHeaders() as $h) {
                $headers[] = $h;
            }
        }
        if (!$isMultipart && array_key_exists('body', $opts) && $opts['body'] !== null) {
            $headers[] = 'Content-Type: application/json';
        }
        if (!empty($opts['idempotencyKey'])) {
            $headers[] = 'Idempotency-Key: ' . $opts['idempotencyKey'];
        }
        if (!empty($opts['headers']) && is_array($opts['headers'])) {
            foreach ($opts['headers'] as $k => $v) {
                $headers[] = $k . ': ' . $v;
            }
        }
        return $headers;
    }

    /** @return string[] */
    private function authHeaders(): array
    {
        if ($this->apiKey !== null && $this->apiKey !== '') {
            if (self::looksLikeApiKey($this->apiKey)) {
                return ['X-Api-Key: ' . $this->apiKey];
            }
            return ['Authorization: Bearer ' . $this->apiKey];
        }
        if ($this->accessToken !== null && $this->accessToken !== '') {
            return ['Authorization: Bearer ' . $this->accessToken];
        }
        return [];
    }

    public static function looksLikeApiKey(string $token): bool
    {
        return strpos($token, 'mk_live_') === 0 || strpos($token, 'mk_test_') === 0;
    }

    /**
     * @param array<string,string|\CURLFile> $multipart
     * @return array<string,mixed>
     */
    private function buildMultipart(array $multipart): array
    {
        $fields = [];
        foreach ($multipart as $name => $value) {
            if ($value instanceof \CURLFile) {
                $fields[$name] = $value;
            } elseif (is_string($value) && is_file($value)) {
                $fields[$name] = new \CURLFile($value);
            } else {
                $fields[$name] = $value;
            }
        }
        return $fields;
    }

    /**
     * @return mixed
     */
    private function parseEnvelope(int $status, string $rawBody)
    {
        if ($status === 204 || $rawBody === '') {
            return null;
        }
        $decoded = json_decode($rawBody, true);
        if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new MosendNetworkException('Respuesta JSON inválida: ' . json_last_error_msg());
        }
        if (is_array($decoded) && array_key_exists('data', $decoded)) {
            return $decoded['data'];
        }
        return $decoded;
    }

    /** @param array<string,string> $headers */
    private function buildError(int $status, string $rawBody, array $headers): MosendApiException
    {
        $body = null;
        if ($rawBody !== '') {
            $decoded = json_decode($rawBody, true);
            $body = (json_last_error() === JSON_ERROR_NONE) ? $decoded : $rawBody;
        }
        $retryAfter = null;
        if (isset($headers['retry-after']) && is_numeric($headers['retry-after'])) {
            $retryAfter = (int) $headers['retry-after'];
        } elseif (isset($headers['x-ratelimit-reset']) && is_numeric($headers['x-ratelimit-reset'])) {
            $retryAfter = (int) $headers['x-ratelimit-reset'];
        }
        $requestId = $headers['x-request-id'] ?? null;
        return ErrorFactory::build($status, $body, $requestId, $retryAfter);
    }

    /** @param array<string,string> $headers */
    private function readMeta(int $status, array $headers): RawResponse
    {
        $num = static function (?string $v): ?int {
            return ($v !== null && is_numeric($v)) ? (int) $v : null;
        };
        return new RawResponse(
            $status,
            $headers['x-request-id'] ?? null,
            $num($headers['x-ratelimit-limit'] ?? null),
            $num($headers['x-ratelimit-remaining'] ?? null),
            $num($headers['x-ratelimit-reset'] ?? null)
        );
    }

    /** @return array<string,string> */
    private function parseHeaders(string $raw): array
    {
        $out = [];
        foreach (preg_split('/\r\n|\n/', $raw) ?: [] as $line) {
            $pos = strpos($line, ':');
            if ($pos === false) {
                continue;
            }
            $key = strtolower(trim(substr($line, 0, $pos)));
            $out[$key] = trim(substr($line, $pos + 1));
        }
        return $out;
    }

    private function backoffMicros(int $attempt): int
    {
        $base = isset($this->retries['baseDelayMs']) ? (int) $this->retries['baseDelayMs'] : 250;
        $jitter = random_int(0, 100);
        return (int) (($base * (2 ** ($attempt - 1)) + $jitter) * 1000);
    }

    /** @param array<string,string> $headers */
    private function retryAfterMicros(array $headers, int $attempt): int
    {
        if (isset($headers['retry-after']) && is_numeric($headers['retry-after'])) {
            return (int) ((float) $headers['retry-after'] * 1_000_000);
        }
        return $this->backoffMicros($attempt);
    }
}
