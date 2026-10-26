<?php

declare(strict_types=1);

namespace Mosend\Resources;

/**
 * `/organizations/{orgId}/agenda` — agenda de citas: configuración, servicios,
 * disponibilidad, días bloqueados, citas, recursos, paquetes, lista de espera,
 * reporte, feed ICS, Google Calendar y el Flow de reserva.
 */
final class AgendaResource extends Resource
{
    // ─── Configuración ───────────────────────────────────────────────────────

    /** @return mixed */
    public function getSettings(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/agenda/settings");
    }

    /**
     * Habilitar requiere la función de plan `agenda`.
     *
     * @param array<string,mixed> $input enabled?, minNoticeMin?, maxAdvanceDays?, reminderHoursBefore?,
     *   plantillas (reminder/confirm/reschedule/cancel/espera/volver TemplateName|Lang)?, paginaPublica?,
     *   paginaSlug?, bloquearFestivos?, anticipoMinutos?, … orgId?
     * @return mixed
     */
    public function updateSettings(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/agenda/settings", ['body' => $input]);
    }

    /** @param array<string,mixed> $input tipo(salon|spa|consultorio|asesoria|clases), orgId? @return mixed */
    public function applyPreset(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/agenda/preset", ['body' => $input]);
    }

    // ─── Feed ICS ────────────────────────────────────────────────────────────

    /** Estado del feed ICS (URL de suscripción o deshabilitado). @return mixed */
    public function getIcs(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/agenda/ics");
    }

    /** Genera o rota el token del feed (rotar revoca las suscripciones previas). @return mixed */
    public function rotateIcs(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/agenda/ics/rotate");
    }

    /** @return mixed */
    public function revokeIcs(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('DELETE', "/organizations/{$orgId}/agenda/ics");
    }

    /**
     * `GET /public/agenda/{token}/calendar.ics` (público). Devuelve el texto
     * iCalendar tal cual; lo consumen normalmente Google/Outlook/Apple Calendar.
     */
    public function icsFeed(string $token): string
    {
        return (string) $this->http->request('GET', '/public/agenda/' . rawurlencode($token) . '/calendar.ics', [
            'raw' => true,
            'skipAuth' => true,
        ]);
    }

    // ─── Servicios (tipos de cita) ───────────────────────────────────────────

    /** @return mixed */
    public function listTypes(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/agenda/types");
    }

    /**
     * @param array<string,mixed> $input name, durationMin, staffUserIds?, description?, bufferMin?,
     *   bufferBeforeMin?, slotIntervalMin?, price?, currency?, color?, modality?, location?, meetingUrl?,
     *   category?, sortOrder?, capacity?, anticipoTipo?, anticipoValor?, preguntas?, volverEnDias?, recursoId?, orgId?
     * @return mixed
     */
    public function createType(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/agenda/types", ['body' => $input]);
    }

    /** @param array<string,mixed> $input mismos campos que createType (todos opcionales) + active?, orgId? @return mixed */
    public function updateType(string $typeId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/agenda/types/{$typeId}", ['body' => $input]);
    }

    /** Historial resumido de citas de un contacto. @return mixed */
    public function contactSummary(string $contactId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/agenda/contacts/{$contactId}/resumen");
    }

    // ─── Recursos compartidos ────────────────────────────────────────────────

    /** @return mixed */
    public function listResources(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/agenda/recursos");
    }

    /** @param array<string,mixed> $input nombre, cantidad?, orgId? @return mixed */
    public function createResource(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/agenda/recursos", ['body' => $input]);
    }

    /** @param array<string,mixed> $input nombre?, cantidad?, orgId? @return mixed */
    public function updateResource(string $resourceId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/agenda/recursos/{$resourceId}", ['body' => $input]);
    }

    /** @return mixed */
    public function deleteResource(string $resourceId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('DELETE', "/organizations/{$orgId}/agenda/recursos/{$resourceId}");
    }

    // ─── Paquetes de sesiones ────────────────────────────────────────────────

    /** @param array<string,mixed> $query contactId?, orgId? @return mixed */
    public function listPackages(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/agenda/paquetes", ['query' => $query]);
    }

    /** @param array<string,mixed> $input contactId, nombre, sesiones, appointmentTypeId?, usadas?, venceAt?, orgId? @return mixed */
    public function createPackage(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/agenda/paquetes", ['body' => $input]);
    }

    /** Anula un paquete de sesiones. @return mixed */
    public function voidPackage(string $packageId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('DELETE', "/organizations/{$orgId}/agenda/paquetes/{$packageId}");
    }

    // ─── Reporte y lista de espera ───────────────────────────────────────────

    /** @param array<string,mixed> $query desde?, hasta? (YYYY-MM-DD), orgId? @return mixed */
    public function report(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/agenda/reporte", ['query' => $query]);
    }

    /** @param array<string,mixed> $query estado?(ESPERANDO|OFRECIDA|AGENDADA|CANCELADA|VENCIDA), contactId?, orgId? @return mixed */
    public function listWaitlist(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/agenda/lista-de-espera", ['query' => $query]);
    }

    /** @param array<string,mixed> $input contactId, typeId, desde, hasta, staffUserId?, notas?, conversationId?, phoneNumberId?, orgId? @return mixed */
    public function addToWaitlist(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/agenda/lista-de-espera", ['body' => $input]);
    }

    /** @return mixed */
    public function removeFromWaitlist(string $entryId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('DELETE', "/organizations/{$orgId}/agenda/lista-de-espera/{$entryId}");
    }

    // ─── Profesionales, disponibilidad y días bloqueados ─────────────────────

    /** @return mixed */
    public function listStaff(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/agenda/staff");
    }

    /** @param array<string,mixed> $query staffUserId?, orgId? @return mixed */
    public function getAvailability(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/agenda/availability", ['query' => $query]);
    }

    /**
     * Reemplaza el set completo de reglas semanales.
     *
     * @param array<string,mixed> $input rules: [{weekday, startMinute, endMinute}], staffUserId? (va en query), orgId?
     * @return mixed
     */
    public function setAvailability(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        $query = $this->staffQuery($input);
        unset($input['orgId'], $input['staffUserId']);
        return $this->http->request('PUT', "/organizations/{$orgId}/agenda/availability", [
            'query' => $query,
            'body' => $input,
        ]);
    }

    /** @param array<string,mixed> $query staffUserId?, orgId? @return mixed */
    public function listExceptions(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/agenda/exceptions", ['query' => $query]);
    }

    /**
     * @param array<string,mixed> $input date (YYYY-MM-DD), dateTo?, startMinute?, endMinute?, reason?,
     *   staffUserId? (va en query), orgId?
     * @return mixed
     */
    public function addException(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        $query = $this->staffQuery($input);
        unset($input['orgId'], $input['staffUserId']);
        return $this->http->request('POST', "/organizations/{$orgId}/agenda/exceptions", [
            'query' => $query,
            'body' => $input,
        ]);
    }

    /** Responde 204. @return mixed */
    public function removeException(string $exceptionId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('DELETE', "/organizations/{$orgId}/agenda/exceptions/{$exceptionId}");
    }

    // ─── Horarios y citas ────────────────────────────────────────────────────

    /** @param array<string,mixed> $query typeId, from?, days?, staffUserId?, seats?, orgId? @return mixed */
    public function getSlots(array $query)
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/agenda/slots", ['query' => $query]);
    }

    /** @param array<string,mixed> $query from?, to?, status?, contactId?, staffUserId?, orgId? @return mixed */
    public function listAppointments(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/agenda/appointments", ['query' => $query]);
    }

    /**
     * Reserva un slot exacto (de getSlots). 409 si ya no está disponible.
     *
     * @param array<string,mixed> $input typeId, contactId, startAt, conversationId?, phoneNumberId?, notes?,
     *   staffUserId?, seats?, cobrarAnticipo?, respuestas?, direccion?, sinPaquete?, repetir?{cadaDias,veces},
     *   meetingUrl?, inviteEmail?, pedirCorreo?, orgId?
     * @return mixed
     */
    public function book(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/agenda/appointments", ['body' => $input]);
    }

    /** @param array<string,mixed> $input meetingUrl?, inviteEmail?, notes?, orgId? @return mixed */
    public function updateAppointment(string $appointmentId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/agenda/appointments/{$appointmentId}", ['body' => $input]);
    }

    /** @param array<string,mixed> $input reason?, serie?, orgId? @return mixed */
    public function cancel(string $appointmentId, array $input = [])
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/agenda/appointments/{$appointmentId}/cancel", ['body' => $input]);
    }

    /** @param array<string,mixed> $input startAt (ISO), orgId? @return mixed */
    public function reschedule(string $appointmentId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/agenda/appointments/{$appointmentId}/reschedule", ['body' => $input]);
    }

    /** @param array<string,mixed> $input status(COMPLETED|NO_SHOW), orgId? @return mixed */
    public function setStatus(string $appointmentId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/agenda/appointments/{$appointmentId}/status", ['body' => $input]);
    }

    /** Le pide al cliente por WhatsApp el correo para la invitación. @return mixed */
    public function requestEmail(string $appointmentId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/agenda/appointments/{$appointmentId}/pedir-correo");
    }

    /** Citas agendadas sin terminar, cuántas son hoy y la próxima. @return mixed */
    public function counts(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/agenda/counts");
    }

    // ─── Google Calendar ─────────────────────────────────────────────────────

    /** @return mixed */
    public function googleStatus(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/agenda/google/status");
    }

    /** URL de consentimiento OAuth para conectar Google: `{ url }`. @return mixed */
    public function googleConnect(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/agenda/google/connect");
    }

    /** @param array<string,mixed> $input pushEnabled?, blockBusyEnabled?, crearMeet?, orgId? @return mixed */
    public function updateGoogle(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/agenda/google", ['body' => $input]);
    }

    /** @return mixed */
    public function disconnectGoogle(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('DELETE', "/organizations/{$orgId}/agenda/google");
    }

    /**
     * `GET /agenda/google/callback` (público). Es el destino del consentimiento
     * OAuth de Google: lo abre el navegador, no una integración. La API responde
     * con una redirección al panel; aquí se devuelve la URL de destino
     * (`…/dashboard/agenda?google=connected|error`).
     *
     * @param array<string,mixed> $query code?, state?, error?
     */
    public function googleCallback(array $query): string
    {
        return (string) $this->http->request('GET', '/agenda/google/callback', [
            'query' => $query,
            'raw' => true,
            'skipAuth' => true,
        ]);
    }

    // ─── Flow de reserva (WhatsApp Flows) ────────────────────────────────────

    /** @param array<string,mixed> $input contactId, appointmentTypeId, conversationId?, phoneNumberId?, orgId? @return mixed */
    public function sendBookingFlow(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/agenda/booking-flow/send", ['body' => $input]);
    }

    /**
     * `POST /agenda/flow/data` (público): endpoint de datos que llama Meta con
     * la petición cifrada del Flow. Devuelve la respuesta cifrada (base64) en
     * texto plano.
     *
     * @param array<string,mixed> $input encrypted_flow_data, encrypted_aes_key, initial_vector
     */
    public function bookingFlowData(array $input): string
    {
        return (string) $this->http->request('POST', '/agenda/flow/data', [
            'body' => $input,
            'raw' => true,
            'skipAuth' => true,
        ]);
    }

    /**
     * @param array<string,mixed> $input
     * @return array<string,mixed>
     */
    private function staffQuery(array $input): array
    {
        return isset($input['staffUserId']) ? ['staffUserId' => $input['staffUserId']] : [];
    }
}
