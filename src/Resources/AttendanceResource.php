<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/attendance` — fichaje y control de jornada de los agentes. */
final class AttendanceResource extends Resource
{
    /** Estado de jornada del agente autenticado (null = sin jornada). @return mixed */
    public function me(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/attendance/me");
    }

    /** Inicia la jornada (idempotente). @return mixed */
    public function start(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/attendance/start");
    }

    /** @param array<string,mixed> $input status(ONLINE|LUNCH|BREAK|MEETING|TRAINING), orgId? @return mixed */
    public function changeStatus(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/attendance/status", ['body' => $input]);
    }

    /** Finaliza la jornada y congela el informe del día. @param array<string,mixed> $input note?, orgId? @return mixed */
    public function end(array $input = [])
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/attendance/end", ['body' => $input]);
    }

    /** Jornada de hoy del agente (totales, timeline, métricas). @return mixed */
    public function today(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/attendance/today");
    }

    /** Configuración de auto-cierre de jornadas (admin). @return mixed */
    public function getSettings(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/attendance/settings");
    }

    /**
     * Actualiza la configuración de auto-cierre (admin).
     *
     * @param array<string,mixed> $input enabled?, autoCloseEnabled?, inactivityMinutes?,
     *   closeAtShiftEnd?, requireJornadaForInbox?, allowOvertime?, requireActiveToAttend?, orgId?
     * @return mixed
     */
    public function updateSettings(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/attendance/settings", ['body' => $input]);
    }

    /** Agentes con turno extra concedido para hoy (admin). @return mixed */
    public function overtimeToday(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/attendance/overtime");
    }

    /** Concede/revoca turno extra de un agente para hoy (admin). @param array<string,mixed> $input enabled, orgId? @return mixed */
    public function setOvertime(string $agentId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/attendance/overtime/{$agentId}", ['body' => $input]);
    }

    /** Estado en vivo de todos los miembros de la org (admin). @return mixed */
    public function teamStatus(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/attendance/team-status");
    }

    /** Tablero de asistencia de un día (admin). @param array<string,mixed> $query date?, orgId? @return mixed */
    public function attendanceDay(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/attendance/day", ['query' => $query]);
    }

    /** Reporte de horas por agente sobre un rango (admin). @param array<string,mixed> $query from?, to?, orgId? @return mixed */
    public function report(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/attendance/report", ['query' => $query]);
    }

    /** Historial de jornadas del agente autenticado. @param array<string,mixed> $query from?, to?, orgId? @return mixed */
    public function mySessions(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/attendance/sessions", ['query' => $query]);
    }

    /** Historial de jornadas de un agente con su informe (admin). @param array<string,mixed> $query from?, to?, orgId? @return mixed */
    public function agentSessions(string $agentId, array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/attendance/sessions/agent/{$agentId}", ['query' => $query]);
    }

    /** Corrige el cierre de una jornada finalizada (admin). @param array<string,mixed> $input endedAt(epoch ms), orgId? @return mixed */
    public function correctSession(string $sessionId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/attendance/sessions/{$sessionId}", ['body' => $input]);
    }

    /** Elimina una jornada y sus eventos (admin). @return mixed */
    public function deleteSession(string $sessionId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('DELETE', "/organizations/{$orgId}/attendance/sessions/{$sessionId}");
    }
}
