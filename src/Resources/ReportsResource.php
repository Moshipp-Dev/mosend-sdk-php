<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/reports` — reportes y métricas de equipo. */
final class ReportsResource extends Resource
{
    /** @return mixed */
    public function summary(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/reports/summary");
    }

    /** @param array<string,mixed> $query since?, orgId? @return mixed */
    public function messaging(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/reports/messaging", ['query' => $query]);
    }

    /** @return mixed */
    public function billing(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/reports/billing");
    }

    /** @param array<string,mixed> $query @return mixed */
    public function customers(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/reports/customers", ['query' => $query]);
    }

    /** @return mixed */
    public function customer(string $contactId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/reports/customers/{$contactId}");
    }

    /** @param array<string,mixed> $query weeks?, compare?, orgId? @return mixed */
    public function teamWeekly(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/reports/team/weekly", ['query' => $query]);
    }

    /** @param array<string,mixed> $query since?, until?, orgId? @return mixed */
    public function teamByAgent(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/reports/team/by-agent", ['query' => $query]);
    }

    /** @return mixed */
    public function teamGoals(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/reports/team/goals");
    }

    /** @param array<string,mixed> $input @return mixed */
    public function updateTeamGoals(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/reports/team/goals", ['body' => $input]);
    }
}
