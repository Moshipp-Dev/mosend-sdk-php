<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/notifications` — notificaciones del usuario. */
final class NotificationsResource extends Resource
{
    /** @param array<string,mixed> $query @return mixed */
    public function list(array $query = [])
    {
        return $this->http->request('GET', '/notifications', ['query' => $query]);
    }

    /** @return mixed */
    public function markRead(string $id)
    {
        return $this->http->request('PATCH', "/notifications/{$id}/read");
    }
}
