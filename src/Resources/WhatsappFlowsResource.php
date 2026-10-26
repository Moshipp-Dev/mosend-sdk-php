<?php

declare(strict_types=1);

namespace Mosend\Resources;

/**
 * `/organizations/{orgId}/whatsapp-flows` — WhatsApp Flows (formularios nativos
 * de Meta). Para enviarlos a un contacto: `MessagesResource::sendFlow`.
 */
final class WhatsappFlowsResource extends Resource
{
    /** @param array<string,mixed> $query wabaId?, orgId? @return mixed */
    public function list(array $query = [])
    {
        $orgId = $query['orgId'] ?? null;
        unset($query['orgId']);
        return $this->pedir('GET', $orgId, '', ['query' => $query]);
    }

    /**
     * Crea el Flow en Meta (opcionalmente con su flow.json).
     *
     * @param array<string,mixed> $input wabaId, name, categories (SIGN_UP, SIGN_IN, APPOINTMENT_BOOKING,
     *   LEAD_GENERATION, CONTACT_US, CUSTOMER_SUPPORT, SURVEY, OTHER), flowJson? (string), orgId?
     * @return mixed
     */
    public function create(array $input)
    {
        $orgId = $input['orgId'] ?? null;
        unset($input['orgId']);
        return $this->pedir('POST', $orgId, '', ['body' => $input]);
    }

    /** Reconcilia los Flows locales con los que tiene Meta para esa WABA. @return mixed */
    public function sync(string $wabaId, ?string $orgId = null)
    {
        return $this->pedir('POST', $orgId, "/sync/{$wabaId}");
    }

    /** Detalle + estado, vista previa y errores de validación de Meta. @return mixed */
    public function get(string $id, ?string $orgId = null)
    {
        return $this->pedir('GET', $orgId, "/{$id}");
    }

    /** Reemplaza el contenido (flow.json serializado). @return mixed */
    public function updateJson(string $id, string $flowJson, ?string $orgId = null)
    {
        return $this->pedir('PATCH', $orgId, "/{$id}/json", ['body' => ['flowJson' => $flowJson]]);
    }

    /** @param array<string,mixed> $input name?, categories?, orgId? @return mixed */
    public function update(string $id, array $input)
    {
        $orgId = $input['orgId'] ?? null;
        unset($input['orgId']);
        return $this->pedir('PATCH', $orgId, "/{$id}", ['body' => $input]);
    }

    /** @return mixed */
    public function publish(string $id, ?string $orgId = null)
    {
        return $this->pedir('POST', $orgId, "/{$id}/publish");
    }

    /** Marca el Flow como obsoleto (un Flow publicado no se puede borrar). @return mixed */
    public function deprecate(string $id, ?string $orgId = null)
    {
        return $this->pedir('POST', $orgId, "/{$id}/deprecate");
    }

    /** Borra un Flow en borrador. @return mixed */
    public function delete(string $id, ?string $orgId = null)
    {
        return $this->pedir('DELETE', $orgId, "/{$id}");
    }

    /**
     * @param array<string,mixed> $opts
     * @return mixed
     */
    private function pedir(string $method, ?string $orgId, string $ruta, array $opts = [])
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request($method, "/organizations/{$orgId}/whatsapp-flows{$ruta}", $opts);
    }
}
