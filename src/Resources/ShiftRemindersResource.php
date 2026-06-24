<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/shift-reminders` — recordatorios de jornada de los agentes. */
final class ShiftRemindersResource extends Resource
{
    /** Lee los recordatorios de jornada. @return mixed */
    public function get(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/shift-reminders");
    }

    /**
     * Actualiza (parcial) los recordatorios de jornada.
     *
     * @param array<string,mixed> $input enabled?, channels?{push,inApp,email},
     *   events?{shiftStart,lunchStart,lunchEnd,shiftEnd}, shiftStartLeadMin?,
     *   shiftEndLeadMin?, orgId?
     * @return mixed
     */
    public function update(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PUT', "/organizations/{$orgId}/shift-reminders", ['body' => $input]);
    }
}
