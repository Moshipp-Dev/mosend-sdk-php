<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/permissions` — catálogo global de permisos. */
final class PermissionsResource extends Resource
{
    /** @return mixed */
    public function list()
    {
        return $this->http->request('GET', '/permissions');
    }
}
