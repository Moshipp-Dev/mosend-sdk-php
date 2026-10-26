<?php

declare(strict_types=1);

namespace Mosend\Resources;

/**
 * `/organizations/{orgId}/whatsapp-groups` — grupos de WhatsApp creados desde un
 * número de la org. La creación es asíncrona: el id del grupo y el enlace llegan
 * por webhook (`estado` pasa de PENDIENTE a ACTIVO o FALLIDO).
 */
final class WhatsappGroupsResource extends Resource
{
    /** @param array<string,mixed> $query phoneNumberId?, orgId? @return mixed */
    public function list(array $query = [])
    {
        $orgId = $query['orgId'] ?? null;
        unset($query['orgId']);
        return $this->pedir('GET', $orgId, '', ['query' => $query]);
    }

    /**
     * @param array<string,mixed> $input phoneNumberId, subject (máx. 128), description? (máx. 2048),
     *   joinApprovalMode? ('auto_approve'|'approval_required'), orgId?
     * @return mixed
     */
    public function create(array $input)
    {
        $orgId = $input['orgId'] ?? null;
        unset($input['orgId']);
        return $this->pedir('POST', $orgId, '', ['body' => $input]);
    }

    /** Un grupo con sus participantes. @return mixed */
    public function get(string $id, ?string $orgId = null)
    {
        return $this->pedir('GET', $orgId, "/{$id}");
    }

    /** Elimina el grupo y saca a todos sus participantes. @return mixed */
    public function delete(string $id, ?string $orgId = null)
    {
        return $this->pedir('DELETE', $orgId, "/{$id}");
    }

    /** Enlace de invitación vigente. @return mixed */
    public function getInviteLink(string $id, ?string $orgId = null)
    {
        return $this->pedir('GET', $orgId, "/{$id}/invite-link");
    }

    /** Revoca el enlace anterior y genera uno nuevo. @return mixed */
    public function resetInviteLink(string $id, ?string $orgId = null)
    {
        return $this->pedir('POST', $orgId, "/{$id}/invite-link");
    }

    /**
     * Saca participantes (máx. 8 por llamada). Quien sale ya no puede volver por el enlace.
     *
     * @param string[] $waIds
     * @return mixed
     */
    public function removeParticipants(string $id, array $waIds, ?string $orgId = null)
    {
        return $this->pedir('DELETE', $orgId, "/{$id}/participants", ['body' => ['waIds' => $waIds]]);
    }

    /** Solicitudes de ingreso pendientes (grupos con approval_required). @return mixed */
    public function listJoinRequests(string $id, ?string $orgId = null)
    {
        return $this->pedir('GET', $orgId, "/{$id}/join-requests");
    }

    /**
     * Aprueba o rechaza solicitudes de ingreso (máx. 50 por llamada).
     *
     * @param string[] $joinRequestIds
     * @param string $accion 'aprobar'|'rechazar'
     * @return mixed
     */
    public function resolveJoinRequests(string $id, array $joinRequestIds, string $accion, ?string $orgId = null)
    {
        return $this->pedir('POST', $orgId, "/{$id}/join-requests", [
            'body' => ['joinRequestIds' => $joinRequestIds, 'accion' => $accion],
        ]);
    }

    /**
     * @param array<string,mixed> $opts
     * @return mixed
     */
    private function pedir(string $method, ?string $orgId, string $ruta, array $opts = [])
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request($method, "/organizations/{$orgId}/whatsapp-groups{$ruta}", $opts);
    }
}
