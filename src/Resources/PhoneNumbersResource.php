<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/phone-numbers` — números y registro. */
final class PhoneNumbersResource extends Resource
{
    /** @return mixed */
    public function list(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/phone-numbers");
    }

    /** @return mixed */
    public function retrieve(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/phone-numbers/{$id}");
    }

    /** @param array<string,mixed> $input wabaId, cc, phoneNumber, verifiedName, orgId? @return mixed */
    public function create(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/phone-numbers", ['body' => $input]);
    }

    /** @return mixed */
    public function sync(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/phone-numbers/sync");
    }

    public function delete(string $id, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('DELETE', "/organizations/{$orgId}/phone-numbers/{$id}");
    }

    /** @return mixed */
    public function restore(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/phone-numbers/{$id}/restore");
    }

    public function purge(string $id, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('DELETE', "/organizations/{$orgId}/phone-numbers/{$id}/purge");
    }

    /** @param array<string,mixed> $input method ('SMS'|'VOICE'), language?, orgId? */
    public function requestRegistrationCode(string $id, array $input): void
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        $this->http->request('POST', "/organizations/{$orgId}/phone-numbers/{$id}/registration/request-code", ['body' => $input]);
    }

    /** @param array<string,mixed> $input code, orgId? */
    public function verifyRegistrationCode(string $id, array $input): void
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        $this->http->request('POST', "/organizations/{$orgId}/phone-numbers/{$id}/registration/verify-code", ['body' => $input]);
    }

    /** @param array<string,mixed> $input pin, orgId? */
    public function register(string $id, array $input): void
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        $this->http->request('POST', "/organizations/{$orgId}/phone-numbers/{$id}/registration/register", ['body' => $input]);
    }

    public function deregister(string $id, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('POST', "/organizations/{$orgId}/phone-numbers/{$id}/registration/deregister");
    }

    /**
     * Cambia el color del número en el inbox (null = color automático). Paleta: emerald,
     * sky, violet, amber, rose, indigo, teal, fuchsia, orange, lime, cyan, pink, slate.
     *
     * @return mixed
     */
    public function update(string $id, ?string $color, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('PATCH', "/organizations/{$orgId}/phone-numbers/{$id}", [
            'body' => ['color' => $color],
        ]);
    }

    /** Mensajes de servicio del mes por número, contra los 1.000 gratis de Meta. @return mixed */
    public function serviceUsage(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/phone-numbers/service-usage");
    }

    /** Desconexiones y reconexiones del número, con su motivo. @return mixed */
    public function connectionHistory(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/phone-numbers/{$id}/historial-de-conexion");
    }

    /** Qué deja de funcionar si se archiva el número. @return mixed */
    public function archiveImpact(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/phone-numbers/{$id}/impacto-al-archivar");
    }

    /**
     * Coexistencia: pide a Meta los contactos y el historial de la app WhatsApp Business
     * (ventana de 24 h desde el registro, una sola vez).
     *
     * @return mixed
     */
    public function syncCoexistence(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/phone-numbers/{$id}/coexistence/sync");
    }

    /**
     * Re-verifica el número contra Meta y sana la marca de desconexión si volvió a
     * responder; si no, indica rehacer la conexión.
     *
     * @return mixed
     */
    public function reconnect(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/phone-numbers/{$id}/reconnect");
    }

    /**
     * Fija el username de negocio del número (letras, números, punto y guion bajo; 3-30).
     *
     * @param array<string,mixed> $input username, transferAction? ('none'|'force_transfer'), orgId?
     * @return mixed
     */
    public function setUsername(string $id, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/phone-numbers/{$id}/username", ['body' => $input]);
    }
}
