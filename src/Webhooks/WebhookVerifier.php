<?php

declare(strict_types=1);

namespace Mosend\Webhooks;

use Mosend\Exception\MosendWebhookSignatureException;

/**
 * Verificación de webhooks salientes de Mosend. Cada request trae el header
 * `X-Mosend-Signature: sha256=<hex>` calculado con HMAC-SHA256 sobre el body
 * CRUDO (no parseado). Validá la firma antes de procesar el evento.
 */
final class WebhookVerifier
{
    /** Calcula la firma esperada para un body + secreto. */
    public static function computeSignature(string $rawBody, string $secret): string
    {
        return 'sha256=' . hash_hmac('sha256', $rawBody, $secret);
    }

    /**
     * ¿La firma del header coincide con el body? Comparación timing-safe.
     */
    public static function verify(string $rawBody, ?string $signature, string $secret): bool
    {
        if ($signature === null || $signature === '' || $secret === '') {
            return false;
        }
        $expected = self::computeSignature($rawBody, $secret);
        return hash_equals($expected, $signature);
    }

    /**
     * Valida la firma y devuelve el evento parseado (array asociativo). Lanza
     * MosendWebhookSignatureException si la firma es inválida.
     *
     * @return array<string,mixed>
     */
    public static function parseEvent(string $rawBody, ?string $signature, string $secret): array
    {
        if (!self::verify($rawBody, $signature, $secret)) {
            throw new MosendWebhookSignatureException();
        }
        $decoded = json_decode($rawBody, true);
        return is_array($decoded) ? $decoded : [];
    }
}
