<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/usage/daily` — uso diario. */
final class UsageResource extends Resource
{
    /** @param array<string,mixed> $query metric?, since?, orgId? @return mixed */
    public function daily(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/usage/daily", ['query' => $query]);
    }
}
