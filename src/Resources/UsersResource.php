<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/users/me` — usuario autenticado. */
final class UsersResource extends Resource
{
    /** @return mixed */
    public function me()
    {
        return $this->http->request('GET', '/users/me');
    }

    /** @param array<string,mixed> $input name?, locale? @return mixed */
    public function updateMe(array $input)
    {
        return $this->http->request('PATCH', '/users/me', ['body' => $input]);
    }

    /** @param array<string,mixed> $input currentPassword, newPassword */
    public function changePassword(array $input): void
    {
        $this->http->request('POST', '/users/me/change-password', ['body' => $input]);
    }
}
