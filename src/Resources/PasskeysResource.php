<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/passkeys/*` (+ login público en /auth/passkey/login/*). */
final class PasskeysResource extends Resource
{
    /** @return mixed */
    public function registrationOptions()
    {
        return $this->http->request('POST', '/passkeys/registration/options');
    }

    /** @param array<string,mixed> $input @return mixed */
    public function registrationVerify(array $input)
    {
        return $this->http->request('POST', '/passkeys/registration/verify', ['body' => $input]);
    }

    /** @return mixed */
    public function list()
    {
        return $this->http->request('GET', '/passkeys');
    }

    /** @param array<string,mixed> $input name @return mixed */
    public function rename(string $id, array $input)
    {
        return $this->http->request('PATCH', "/passkeys/{$id}", ['body' => $input]);
    }

    public function delete(string $id): void
    {
        $this->http->request('DELETE', "/passkeys/{$id}");
    }

    /** @param array<string,mixed> $input email @return mixed */
    public function loginOptions(array $input)
    {
        return $this->http->request('POST', '/auth/passkey/login/options', ['body' => $input, 'skipAuth' => true]);
    }

    /** @param array<string,mixed> $input @return mixed */
    public function loginVerify(array $input)
    {
        return $this->http->request('POST', '/auth/passkey/login/verify', ['body' => $input, 'skipAuth' => true]);
    }
}
