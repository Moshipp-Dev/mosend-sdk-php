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
}
