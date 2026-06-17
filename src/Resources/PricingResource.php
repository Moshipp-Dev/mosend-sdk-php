<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/pricing` — tarifas de conversación. */
final class PricingResource extends Resource
{
    /** @return mixed */
    public function retrieve()
    {
        return $this->http->request('GET', '/pricing');
    }
}
