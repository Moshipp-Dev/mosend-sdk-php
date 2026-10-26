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

    /**
     * Envía un código OTP por WhatsApp para verificar el teléfono del usuario. Requiere sesión JWT.
     * @return mixed
     */
    public function requestPhoneCode()
    {
        return $this->http->request('POST', '/users/me/phone/request-code');
    }

    /**
     * Confirma el código OTP y marca el teléfono como verificado. Requiere sesión JWT.
     *
     * @param array<string,mixed> $input code
     * @return mixed
     */
    public function verifyPhoneCode(array $input)
    {
        return $this->http->request('POST', '/users/me/phone/verify-code', ['body' => $input]);
    }
}
