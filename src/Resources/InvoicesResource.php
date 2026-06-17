<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/invoices` — facturas. */
final class InvoicesResource extends Resource
{
    /** @param array<string,mixed> $query @return mixed */
    public function list(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/invoices", ['query' => $query]);
    }

    /** @return mixed */
    public function retrieve(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/invoices/{$id}");
    }

    /** @return mixed */
    public function pdf(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/invoices/{$id}/pdf");
    }
}
