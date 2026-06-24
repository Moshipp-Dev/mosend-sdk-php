<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/attendance` — horario semanal por agente (semanas ISO). */
final class ScheduleResource extends Resource
{
    /** Semana ISO actual según la TZ de la organización. @return mixed */
    public function currentWeek(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/attendance/current-week");
    }

    /** Horarios del equipo para una semana (admin). @param array<string,mixed> $query isoYear?, isoWeek?, orgId? @return mixed */
    public function listWeek(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/attendance/schedules", ['query' => $query]);
    }

    /** Horario de un agente para una semana (admin). @param array<string,mixed> $query isoYear?, isoWeek?, orgId? @return mixed */
    public function getForAgent(string $agentId, array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/attendance/schedules/{$agentId}", ['query' => $query]);
    }

    /**
     * Asigna/actualiza el horario de un agente (admin).
     *
     * @param array<string,mixed> $input isoYear, isoWeek, days, lunch?, copyToWeeks?, orgId?
     * @return mixed
     */
    public function upsert(string $agentId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PUT', "/organizations/{$orgId}/attendance/schedules/{$agentId}", ['body' => $input]);
    }

    /** Horario del agente autenticado (semana actual si no se pasa). @param array<string,mixed> $query isoYear?, isoWeek?, orgId? @return mixed */
    public function mySchedule(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/attendance/my-schedule", ['query' => $query]);
    }
}
