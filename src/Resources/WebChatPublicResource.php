<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/web-chat/{token}/*` — endpoints públicos que consume el widget. */
final class WebChatPublicResource extends Resource
{
    /** @param array<string,mixed> $input visitorId, mode, ... @return mixed */
    public function createSession(string $token, array $input)
    {
        return $this->http->request('POST', "/web-chat/{$token}/sessions", ['body' => $input]);
    }

    /** @param array<string,mixed> $input email */
    public function resendOtp(string $token, array $input): void
    {
        $this->http->request('POST', "/web-chat/{$token}/sessions/resend-otp", ['body' => $input]);
    }

    /** @param array<string,mixed> $query sessionToken?, limit?, before? @return mixed */
    public function history(string $token, array $query = [])
    {
        return $this->http->request('GET', "/web-chat/{$token}/history", ['query' => $query]);
    }

    /** @param array<string,mixed> $input email, name? @return mixed */
    public function linkEmail(string $token, array $input)
    {
        return $this->http->request('POST', "/web-chat/{$token}/sessions/link-email", ['body' => $input]);
    }

    /**
     * @param string|\CURLFile $file
     * @return mixed
     */
    public function upload(string $token, $file)
    {
        return $this->http->request('POST', "/web-chat/{$token}/upload", ['multipart' => ['file' => $file]]);
    }

    /** @param array<string,mixed> $input caption?, attachment @return mixed */
    public function sendMessage(string $token, array $input)
    {
        return $this->http->request('POST', "/web-chat/{$token}/messages", ['body' => $input]);
    }

    /** Clave VAPID pública para Web Push (cadena vacía si el push está apagado). @return mixed */
    public function getVapidKey(string $token)
    {
        return $this->http->request('GET', "/web-chat/{$token}/push/vapid", ['skipAuth' => true]);
    }

    /**
     * Registra la suscripción Web Push del visitante. Requiere el JWT del visitante.
     *
     * @param array<string,mixed> $subscription endpoint, keys {p256dh, auth}
     * @return mixed
     */
    public function pushSubscribe(string $token, string $visitorJwt, array $subscription)
    {
        return $this->http->request('POST', "/web-chat/{$token}/push/subscribe", [
            'body' => ['subscription' => $subscription],
            'skipAuth' => true,
            'headers' => ['Authorization' => 'Bearer ' . $visitorJwt],
        ]);
    }

    /** Elimina la suscripción Web Push del visitante (por endpoint). @return mixed */
    public function pushUnsubscribe(string $token, string $visitorJwt, string $endpoint)
    {
        return $this->http->request('POST', "/web-chat/{$token}/push/unsubscribe", [
            'body' => ['endpoint' => $endpoint],
            'skipAuth' => true,
            'headers' => ['Authorization' => 'Bearer ' . $visitorJwt],
        ]);
    }

    /**
     * Presencia del visitante: `away` '1' lo marca fuera de línea al instante, '0' en línea.
     * El JWT del visitante va en el cuerpo (como en sendBeacon).
     *
     * @return mixed
     */
    public function presence(string $token, string $visitorJwt, bool $away)
    {
        return $this->http->request('POST', "/web-chat/{$token}/presence", [
            'body' => ['jwt' => $visitorJwt, 'away' => $away ? '1' : '0'],
            'skipAuth' => true,
        ]);
    }

    /** @param array<string,mixed> $input message, stack?, url?, kind? @return mixed */
    public function reportClientError(string $token, array $input)
    {
        return $this->http->request('POST', "/web-chat/{$token}/client-error", [
            'body' => $input,
            'skipAuth' => true,
        ]);
    }
}
