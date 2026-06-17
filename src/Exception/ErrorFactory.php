<?php

declare(strict_types=1);

namespace Mosend\Exception;

/**
 * Construye la excepción adecuada a partir de una respuesta de error del
 * backend. Mapea el status HTTP a la subclase correspondiente y extrae el
 * mensaje, `error` (code), `path` y, si vienen, `metaCode`/`metaSubcode`.
 */
final class ErrorFactory
{
    /**
     * @param array<string,mixed>|string|null $body
     */
    public static function build(int $status, $body, ?string $requestId, ?int $retryAfterSec): MosendApiException
    {
        $parsed = is_array($body) ? $body : null;

        $message = 'HTTP ' . $status;
        if ($parsed !== null && isset($parsed['message'])) {
            $message = is_array($parsed['message'])
                ? implode('; ', $parsed['message'])
                : (string) $parsed['message'];
        } elseif (is_string($body) && $body !== '') {
            $message = $body;
        }

        $extra = [
            'code' => $parsed['error'] ?? 'api_error',
            'body' => $body,
            'requestId' => $requestId,
        ];
        if (isset($parsed['path'])) {
            $extra['path'] = $parsed['path'];
        }
        if (isset($parsed['metaCode'])) {
            $extra['metaCode'] = (int) $parsed['metaCode'];
        }
        if (isset($parsed['metaSubcode'])) {
            $extra['metaSubcode'] = (int) $parsed['metaSubcode'];
        }

        switch ($status) {
            case 400:
                return new MosendBadRequestException($message, $status, $extra);
            case 401:
                return new MosendAuthException($message, $status, $extra);
            case 402:
                return new MosendPaymentRequiredException($message, $status, $extra);
            case 403:
                return new MosendForbiddenException($message, $status, $extra);
            case 404:
                return new MosendNotFoundException($message, $status, $extra);
            case 409:
                return new MosendConflictException($message, $status, $extra);
            case 422:
                return new MosendUnprocessableException($message, $status, $extra);
            case 429:
                $extra['retryAfterSec'] = $retryAfterSec;
                return new MosendRateLimitException($message, $status, $extra);
            default:
                if ($status >= 500) {
                    return new MosendServerException($message, $status, $extra);
                }
                return new MosendApiException($message, $status, $extra);
        }
    }
}
