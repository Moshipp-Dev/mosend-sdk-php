<?php

declare(strict_types=1);

namespace Mosend;

use Mosend\Http\HttpClient;
use Mosend\Resources\ConversationsResource;
use Mosend\Resources\HealthResource;
use Mosend\Resources\MessagesResource;

/**
 * Cliente principal del SDK de Mosend. Instancia con tu API key (y opcionalmente
 * tu orgId default) y accedé a los resources tipados:
 *
 *   $mosend = new MosendClient(['apiKey' => 'mk_live_...', 'orgId' => '...']);
 *   $mosend->messages->send([...]);
 */
final class MosendClient
{
    /** @var HealthResource */
    public $health;
    /** @var MessagesResource */
    public $messages;
    /** @var ConversationsResource */
    public $conversations;

    /** @var HttpClient */
    private $http;

    /**
     * @param array<string,mixed> $config apiKey, accessToken, orgId, baseUrl, timeout(ms), retries, userAgent, defaultHeaders
     */
    public function __construct(array $config = [])
    {
        $httpConfig = [
            'baseUrl' => $config['baseUrl'] ?? 'https://api.mosend.dev',
            'timeoutMs' => $config['timeout'] ?? 30000,
            'userAgent' => $config['userAgent'] ?? 'moshipp-mosend-sdk-php/1.0.0',
            'defaultHeaders' => $config['defaultHeaders'] ?? [],
            'retries' => $config['retries'] ?? null,
        ];
        if (isset($config['apiKey'])) {
            $httpConfig['apiKey'] = $config['apiKey'];
        }
        if (isset($config['accessToken'])) {
            $httpConfig['accessToken'] = $config['accessToken'];
        }

        $this->http = new HttpClient($httpConfig);
        $orgId = isset($config['orgId']) ? (string) $config['orgId'] : null;

        $this->health = new HealthResource($this->http, $orgId);
        $this->messages = new MessagesResource($this->http, $orgId);
        $this->conversations = new ConversationsResource($this->http, $orgId);
    }

    public function setApiKey(?string $key): void
    {
        $this->http->setApiKey($key);
    }

    public function setAccessToken(?string $token): void
    {
        $this->http->setAccessToken($token);
    }

    public function getHttpClient(): HttpClient
    {
        return $this->http;
    }
}
