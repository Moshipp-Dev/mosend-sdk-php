<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/attendance/holidays|leaves` — festivos y novedades (vacaciones, incapacidades…). */
final class DayOffResource extends Resource
{
    /**
     * Festivos de la organización (opcionalmente de un año).
     *
     * @param array<string,mixed> $query year?, orgId?
     * @return mixed
     */
    public function listHolidays(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/attendance/holidays", ['query' => $query]);
    }

    /**
     * Crea o actualiza festivos (varios a la vez).
     *
     * @param array<string,mixed> $input items [{date, name, paid?}], orgId?
     * @return mixed
     */
    public function upsertHolidays(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/attendance/holidays", ['body' => $input]);
    }

    /**
     * Siembra el calendario oficial de festivos de un país para un año (por defecto CO).
     *
     * @param array<string,mixed> $query year, country?, orgId?
     * @return mixed
     */
    public function seedHolidays(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/attendance/holidays/seed", ['query' => $query]);
    }

    /**
     * Elimina un festivo.
     */
    public function deleteHoliday(string $id, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('DELETE', "/organizations/{$orgId}/attendance/holidays/{$id}");
    }

    /**
     * Novedades que tocan el rango dado.
     *
     * @param array<string,mixed> $query from?, to?, agentId?, orgId?
     * @return mixed
     */
    public function listLeaves(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/attendance/leaves", ['query' => $query]);
    }

    /**
     * Registra una novedad de un asesor.
     *
     * @param array<string,mixed> $input agentUserId, type (VACATION|SICK|PERMISSION|UNPAID|OTHER), startDate, endDate, note?, orgId?
     * @return mixed
     */
    public function createLeave(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/attendance/leaves", ['body' => $input]);
    }

    /**
     * Elimina una novedad.
     */
    public function deleteLeave(string $id, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('DELETE', "/organizations/{$orgId}/attendance/leaves/{$id}");
    }
}
