<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/ai-credits` — créditos de IA. */
final class AiCreditsResource extends Resource
{
    /** @return mixed */
    public function summary(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/ai-credits/summary");
    }

    /** @param array<string,mixed> $query limit?, orgId? @return mixed */
    public function transactions(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/ai-credits/transactions", ['query' => $query]);
    }
}
