<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/audit` (+ /admin/audit). */
final class AuditResource extends Resource
{
    /** @param array<string,mixed> $query @return mixed */
    public function list(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/audit", ['query' => $query]);
    }

    /** @param array<string,mixed> $query @return mixed */
    public function export(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/audit/export", ['query' => $query]);
    }

    /** @param array<string,mixed> $query @return mixed */
    public function listAdmin(array $query = [])
    {
        return $this->http->request('GET', '/admin/audit', ['query' => $query]);
    }

    /** @param array<string,mixed> $query @return mixed */
    public function exportAdmin(array $query = [])
    {
        return $this->http->request('GET', '/admin/audit/export', ['query' => $query]);
    }
}
