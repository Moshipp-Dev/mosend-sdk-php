<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/invitations` (+ accept público). */
final class InvitationsResource extends Resource
{
    /** @return mixed */
    public function list(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/invitations");
    }

    /** @param array<string,mixed> $input email, roleId, orgId? @return mixed */
    public function create(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/invitations", ['body' => $input]);
    }

    public function revoke(string $id, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('DELETE', "/organizations/{$orgId}/invitations/{$id}");
    }

    /** @return mixed */
    public function resend(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/invitations/{$id}/resend");
    }

    /** @param array<string,mixed> $input token @return mixed */
    public function accept(array $input)
    {
        return $this->http->request('POST', '/invitations/accept', ['body' => $input]);
    }

    /**
     * Datos públicos de una invitación por token (org, rol, email, si ya tiene cuenta). Sin autenticación.
     * @return mixed
     */
    public function lookup(string $token)
    {
        return $this->http->request('GET', '/invitations/lookup', ['query' => ['token' => $token], 'skipAuth' => true]);
    }

    /**
     * Datos públicos de una activación por token (org, rol, email, nombre). Sin autenticación.
     * @return mixed
     */
    public function activateLookup(string $token)
    {
        return $this->http->request('GET', '/invitations/activate-lookup', ['query' => ['token' => $token], 'skipAuth' => true]);
    }

    /**
     * Acepta la invitación creando la cuenta en el mismo paso; devuelve una sesión lista para usar.
     *
     * @param array<string,mixed> $input token, name, password
     * @return mixed
     */
    public function acceptSignup(array $input)
    {
        return $this->http->request('POST', '/invitations/accept-signup', ['body' => $input, 'skipAuth' => true]);
    }

    /**
     * Activa una cuenta preregistrada definiendo su contraseña; devuelve una sesión lista para usar.
     *
     * @param array<string,mixed> $input token, password
     * @return mixed
     */
    public function activate(array $input)
    {
        return $this->http->request('POST', '/invitations/activate', ['body' => $input, 'skipAuth' => true]);
    }

    /**
     * Invita a varios correos con un mismo rol. Omite los que ya son miembros o ya tienen invitación pendiente.
     *
     * @param array<string,mixed> $input emails, roleId, wabaIds?, orgId?
     * @return mixed
     */
    public function bulk(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/invitations/bulk", ['body' => $input]);
    }

    /**
     * Crea las cuentas (pendientes) y membresías al invitar, ocupando asiento; cada agente solo define su contraseña.
     *
     * @param array<string,mixed> $input members [{email, name?}], roleId, wabaIds?, orgId?
     * @return mixed
     */
    public function preregister(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/invitations/preregister", ['body' => $input]);
    }

    /**
     * Regenera el enlace de activación de un miembro pendiente; con notify reenvía el correo.
     *
     * @param array<string,mixed> $input notify?, orgId?
     * @return mixed
     */
    public function reactivate(string $membershipId, array $input = [])
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/invitations/activation/{$membershipId}", ['body' => $input]);
    }

    /**
     * Genera un enlace de aceptación fresco (rota el token) sin enviar correo. Falla si ya fue aceptada.
     *
     * @return mixed
     */
    public function link(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/invitations/{$id}/link");
    }
}
