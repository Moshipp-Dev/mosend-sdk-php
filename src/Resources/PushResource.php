<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/push` — web push (VAPID). */
final class PushResource extends Resource
{
    /** @return mixed */
    public function config()
    {
        return $this->http->request('GET', '/push/config');
    }

    /** @param array<string,mixed> $input endpoint, p256dh, auth @return mixed */
    public function subscribe(array $input)
    {
        return $this->http->request('POST', '/push/subscribe', ['body' => $input]);
    }

    /** @param array<string,mixed> $input endpoint */
    public function unsubscribe(array $input): void
    {
        $this->http->request('DELETE', '/push/subscribe', ['body' => $input]);
    }

    /** @param array<string,mixed> $input oldEndpoint, endpoint, p256dh, auth @return mixed */
    public function rotate(array $input)
    {
        return $this->http->request('POST', '/push/rotate', ['body' => $input]);
    }

    /** @param array<string,mixed> $query endpoint @return mixed */
    public function diagnose(array $query)
    {
        return $this->http->request('GET', '/push/diagnose', ['query' => $query]);
    }

    /** @return mixed */
    public function listSubscriptions()
    {
        return $this->http->request('GET', '/push/subscriptions');
    }

    /** @return mixed */
    public function test()
    {
        return $this->http->request('POST', '/push/test');
    }
}
