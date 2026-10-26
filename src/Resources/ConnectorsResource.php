<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/conectores` — sistemas externos (WHMCS, cPanel, WooCommerce…) que usa el bot. */
final class ConnectorsResource extends Resource
{
    /**
     * Lista los conectores de la organización.
     *
     * @return mixed
     */
    public function list(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/conectores");
    }

    /**
     * Crea un conector; las credenciales se guardan cifradas.
     *
     * @param array<string,mixed> $input type, name, baseUrl, authId?, authSecret?, enabled?, enabledActions?, phoneNumberIds?, paymentInstructions?, paymentGateway?, customActions?, orgId?
     * @return mixed
     */
    public function create(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/conectores", ['body' => $input]);
    }

    /**
     * Catálogo cerrado de acciones por tipo de conector, con su nivel de riesgo.
     *
     * @return mixed
     */
    public function catalog(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/conectores/catalogo");
    }

    /**
     * Actualiza un conector.
     *
     * @param array<string,mixed> $input orgId?
     * @return mixed
     */
    public function update(string $conectorId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/conectores/{$conectorId}", ['body' => $input]);
    }

    /**
     * Elimina un conector y sus credenciales.
     */
    public function delete(string $conectorId, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('DELETE', "/organizations/{$orgId}/conectores/{$conectorId}");
    }

    /**
     * Prueba la conexión con una llamada de lectura inocua.
     *
     * @return mixed
     */
    public function test(string $conectorId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/conectores/{$conectorId}/probar");
    }

    /**
     * Ejecuta una acción de lectura del catálogo.
     *
     * @param array<string,mixed> $input actionKey, params?, orgId?
     * @return mixed
     */
    public function execute(string $conectorId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/conectores/{$conectorId}/ejecutar", ['body' => $input]);
    }

    /**
     * Acciones sensibles que esperan aprobación de una persona.
     *
     * @return mixed
     */
    public function listApprovals(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/conectores/aprobaciones");
    }

    /**
     * Aprueba y EJECUTA la acción sensible; el cliente recibe aviso en su chat.
     *
     * @return mixed
     */
    public function approve(string $accionId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/conectores/aprobaciones/{$accionId}/aprobar");
    }

    /**
     * Rechaza la acción sensible; el cliente recibe aviso y espera a un asesor.
     *
     * @return mixed
     */
    public function reject(string $accionId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/conectores/aprobaciones/{$accionId}/rechazar");
    }
}
