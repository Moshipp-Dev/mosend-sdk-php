<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/system-notices/active` — avisos globales del sistema (banner). */
final class SystemNoticesResource extends Resource
{
    /** @return mixed */
    public function active()
    {
        return $this->http->request('GET', '/system-notices/active');
    }
}
