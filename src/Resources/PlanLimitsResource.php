<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/plan-limits` — límites del plan vs uso. */
final class PlanLimitsResource extends Resource
{
    /** @return mixed */
    public function retrieve(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/plan-limits");
    }
}
