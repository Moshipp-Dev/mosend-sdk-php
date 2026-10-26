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

    /**
     * Canales conectados (WhatsApp, Instagram, Web Chat), para onboarding y estados vacíos.
     *
     * @return mixed
     */
    public function channelsSummary(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/reports/channels-summary");
    }

    /**
     * Portada del panel: qué espera ahora y cómo va la atención por día, contra el período anterior.
     *
     * @param array<string,mixed> $query dias?, desde?, hasta?, orgId?
     * @return mixed
     */
    public function portada(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/reports/portada", ['query' => $query]);
    }

    /**
     * Serie del equipo por día/semana/mes, con comparación, canal y SLA.
     *
     * @param array<string,mixed> $query from?, to?, granularity? (day|week|month), compare? (true|1), channel? (WHATSAPP|WEB|INSTAGRAM), orgId?
     * @return mixed
     */
    public function teamSeries(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/reports/team/series", ['query' => $query]);
    }

    /**
     * Reporte de ventas de licencias. Solo propietarios y administradores.
     *
     * @param array<string,mixed> $query desde?, hasta?, agrupar? (dia|semana|mes), moneda?, perfilId?, phoneNumberId?, agentId?, userId?, productId?, anuncioId?, orgId?
     * @return mixed
     */
    public function ventas(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/reports/ventas", ['query' => $query]);
    }
}
