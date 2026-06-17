<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/health/*` — endpoints públicos de estado del servicio. */
final class HealthResource extends Resource
{
    /** @return mixed */
    public function live()
    {
        return $this->http->request('GET', '/health/live');
    }

    /** @return mixed */
    public function ready()
    {
        return $this->http->request('GET', '/health/ready');
    }

    /** @return mixed */
    public function status()
    {
        return $this->http->request('GET', '/health/status');
    }
}
