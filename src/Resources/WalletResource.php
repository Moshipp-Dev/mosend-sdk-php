<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/wallet` — saldo y transacciones. */
final class WalletResource extends Resource
{
    /** @return mixed */
    public function retrieve(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/wallet");
    }

    /** @param array<string,mixed> $query limit?, cursor?, orgId? @return mixed */
    public function transactions(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/wallet/transactions", ['query' => $query]);
    }
}
