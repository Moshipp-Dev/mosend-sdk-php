<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/two-factor/*` — 2FA del usuario. */
final class TwoFactorResource extends Resource
{
    /** @return mixed */
    public function enroll()
    {
        return $this->http->request('POST', '/two-factor/enroll');
    }

    /** @param array<string,mixed> $input token @return mixed */
    public function verify(array $input)
    {
        return $this->http->request('POST', '/two-factor/verify', ['body' => $input]);
    }

    public function disable(): void
    {
        $this->http->request('POST', '/two-factor/disable');
    }
}
