<?php

declare(strict_types=1);

namespace Mosend\Resources;

use Mosend\Pagination\Paginator;

/** `/organizations/{orgId}/conversations` — listado, detalle y acciones. */
final class ConversationsResource extends Resource
{
    /**
     * Una página de conversaciones.
     *
     * @param array<string,mixed> $query status, assigneeUserId, take, cursor, search, orgId?
     * @return array{data:array<int,mixed>,endCursor:?string,hasNextPage:bool}
     */
    public function list(array $query = []): array
    {
        return Paginator::page($this->rawList($query));
    }

    /**
     * Itera TODAS las conversaciones (maneja el cursor por vos).
     *
     * @param array<string,mixed> $query
     * @return \Generator<int,mixed>
     */
    public function iterate(array $query = []): \Generator
    {
        $self = $this;
        return Paginator::iterate(static function (array $params) use ($self) {
            return $self->rawList($params);
        }, $query);
    }

    /** @return mixed */
    public function retrieve(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/conversations/{$id}");
    }

    // ── Ajustes de la org ──

    /** @return mixed */
    public function getMessagingSettings(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/conversations/messaging-settings");
    }

    /**
     * @param array<string,mixed> $input sendReadReceipts?, sendTypingIndicator?, autoOpenContactPanel?,
     *   requireOwnershipToReply?, askCategoryOnClose?, markReadOn? ('ABRIR'|'ATENDER'), contactPanelDefaultTab?,
     *   contactPanelTabs?, contactPanelOpenTab?, contactPanelOpenSubTab?, inboxTabs?, unreadScope? ('open'|'all'), orgId?
     * @return mixed
     */
    public function updateMessagingSettings(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/conversations/messaging-settings", ['body' => $input]);
    }

    /** Correos de aviso por retraso de conversación (y demás correos de la org). @return mixed */
    public function getNotificationSettings(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/conversations/notification-settings");
    }

    /**
     * @param array<string,mixed> $input conversationDelayEmail? {enabled?, toAdmins?, toAssignedAgent?, thresholdMin?},
     *   emails? (mapa clave => bool), orgId?
     * @return mixed
     */
    public function updateNotificationSettings(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/conversations/notification-settings", ['body' => $input]);
    }

    /** @return mixed */
    public function getWorkloadSettings(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/conversations/workload/settings");
    }

    /**
     * @param array<string,mixed> $input slaResponseThresholdMin?, staleConversationThresholdHours?,
     *   autoCloseAfterAgentLastMin?, autoCloseAfterClientLastMin?, autoCloseRespectBusinessHours?,
     *   botMemoryResetHours?, volverAlUltimoAsesor?, siElAsesorNoEstaTrabajando? ('bot'|'otro_asesor'), orgId?
     * @return mixed
     */
    public function updateWorkloadSettings(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/conversations/workload/settings", ['body' => $input]);
    }

    // ── Consultas de la bandeja ──

    /** @param array<string,mixed> $query phoneNumberId?, orgId? @return mixed */
    public function unreadCounts(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/conversations/unread-counts", ['query' => $query]);
    }

    /**
     * Busca en los mensajes de todas las conversaciones.
     *
     * @param array<string,mixed> $query q, from?, to?, direction? ('IN'|'OUT'), hasAttachment?, sentByMe?,
     *   conversationId?, orgId?
     * @return mixed
     */
    public function search(array $query)
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/conversations/search", ['query' => $query]);
    }

    /** @return mixed */
    public function workload(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/conversations/workload");
    }

    /** @param array<string,mixed> $query days?, from?, to?, orgId? @return mixed */
    public function workloadByAgent(string $userId, array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/conversations/workload/agent/{$userId}", ['query' => $query]);
    }

    /** @param array<string,mixed> $query days?, desde?, hasta?, orgId? @return mixed */
    public function activityTrends(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/conversations/activity-trends", ['query' => $query]);
    }

    /** @param array<string,mixed> $query days?, metric? ('volume'|'late'|'avgResponse'), desde?, hasta?, orgId? @return mixed */
    public function activityHeatmap(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/conversations/activity-heatmap", ['query' => $query]);
    }

    /** @param array<string,mixed> $input conversationIds, userId (null = sin asignar), orgId? @return mixed */
    public function bulkAssign(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/conversations/bulk-assign", ['body' => $input]);
    }

    // ── Una conversación ──

    /** @param array<string,mixed> $query limit?, before?, around?, orgId? @return mixed */
    public function listMessages(string $id, array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/conversations/{$id}/messages", ['query' => $query]);
    }

    /**
     * @param array<string,mixed> $input status ('open'|'closed'|'snoozed'), category?, resolutionOutcome?,
     *   followUpAt?, orgId?
     * @return mixed
     */
    public function setStatus(string $id, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/conversations/{$id}/status", ['body' => $input]);
    }

    /** @param array<string,mixed> $input category?, resolutionOutcome?, orgId? @return mixed */
    public function categorize(string $id, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/conversations/{$id}/categorize", ['body' => $input]);
    }

    public function markRead(string $id, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('POST', "/organizations/{$orgId}/conversations/{$id}/read");
    }

    public function markUnread(string $id, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('POST', "/organizations/{$orgId}/conversations/{$id}/unread");
    }

    public function sendTyping(string $id, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('POST', "/organizations/{$orgId}/conversations/{$id}/typing");
    }

    /** @param array<string,mixed> $input userId (null = sin asignar), orgId? @return mixed */
    public function assign(string $id, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/conversations/{$id}/assign", ['body' => $input]);
    }

    /** @return mixed */
    public function claim(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/conversations/{$id}/claim");
    }

    /** @param array<string,mixed> $input reason?, detail?, orgId? @return mixed */
    public function requestHandoff(string $id, array $input = [])
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        // Sin campos no se manda cuerpo: `[]` llegaría como arreglo JSON, no como objeto.
        $opts = $input === [] ? [] : ['body' => $input];
        return $this->http->request('POST', "/organizations/{$orgId}/conversations/{$id}/request-handoff", $opts);
    }

    /** @param array<string,mixed> $input until (ISO o null = reactivar), note?, orgId? @return mixed */
    public function snooze(string $id, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/conversations/{$id}/snooze", ['body' => $input]);
    }

    /** @return mixed */
    public function pin(string $id, bool $pinned, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('PATCH', "/organizations/{$orgId}/conversations/{$id}/pin", [
            'body' => ['pinned' => $pinned],
        ]);
    }

    /** @return mixed */
    public function addTag(string $id, string $tagId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/conversations/{$id}/tags", [
            'body' => ['tagId' => $tagId],
        ]);
    }

    public function removeTag(string $id, string $tagId, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('DELETE', "/organizations/{$orgId}/conversations/{$id}/tags/{$tagId}");
    }

    /** @param array<string,mixed> $query category? ('image'|'video'|'audio'|'doc'), limit?, before?, orgId? @return mixed */
    public function listMedia(string $id, array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/conversations/{$id}/media", ['query' => $query]);
    }

    /** @return mixed */
    public function mediaCounts(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/conversations/{$id}/media/counts");
    }

    /** @param array<string,mixed> $query limit?, before?, orgId? @return mixed */
    public function listLinks(string $id, array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/conversations/{$id}/links", ['query' => $query]);
    }

    /**
     * Fija qué agente del bot atiende la conversación (null = que decida el enrutador).
     *
     * @return mixed
     */
    public function setAgent(string $id, ?string $agentId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('PATCH', "/organizations/{$orgId}/conversations/{$id}/agent", [
            'body' => ['agentId' => $agentId],
        ]);
    }

    /** Devuelve la conversación al bot (lo reactiva conservando el contexto). @return mixed */
    public function returnToBot(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/conversations/{$id}/devolver-al-bot");
    }

    /**
     * Toma el hilo de una conversación que atiende otra app (enrutamiento de
     * conversaciones de Meta).
     *
     * @return mixed
     */
    public function takeThread(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/conversations/{$id}/tomar-hilo");
    }

    /**
     * @param array<string,mixed> $query
     * @return mixed payload crudo del listado
     */
    private function rawList(array $query)
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/conversations", ['query' => $query]);
    }
}
