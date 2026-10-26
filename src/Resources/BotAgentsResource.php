<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/bot/agents` — agentes especializados del bot, su configurador y su probador. */
final class BotAgentsResource extends Resource
{
    /**
     * Lista los agentes de bot de la organización.
     *
     * @param array<string,mixed> $query phoneNumberId?, orgId?
     * @return mixed
     */
    public function list(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/bot/agents", ['query' => $query]);
    }

    /**
     * Crea un agente (mismos campos que CreateBotAgentDto).
     *
     * @param array<string,mixed> $input name, description?, phoneNumberIds?, knowledgeDocIds?, provider?, model?, systemPrompt?, enabledTools?, enabledCapabilities?, routeTags?, ..., orgId?
     * @return mixed
     */
    public function create(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/bot/agents", ['body' => $input]);
    }

    /**
     * Catálogo de capacidades activables por agente.
     *
     * @return mixed
     */
    public function capabilities(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/bot/agents/capabilities");
    }

    /**
     * Proveedores de IA con sus modelos elegibles y si tienen clave configurada.
     *
     * @return mixed
     */
    public function modelos(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/bot/agents/modelos");
    }

    /**
     * Rendimiento por agente (conversaciones, traspasos, ventas, citas, costo…).
     *
     * @param array<string,mixed> $query dias?, desde?, hasta?, orgId?
     * @return mixed
     */
    public function stats(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/bot/agents/stats", ['query' => $query]);
    }

    /**
     * Detalle de un agente.
     *
     * @return mixed
     */
    public function retrieve(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/bot/agents/{$id}");
    }

    /**
     * Actualiza un agente (campos de UpdateBotAgentDto).
     *
     * @param array<string,mixed> $input orgId?
     * @return mixed
     */
    public function update(string $id, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/bot/agents/{$id}", ['body' => $input]);
    }

    /**
     * Elimina un agente.
     */
    public function delete(string $id, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('DELETE', "/organizations/{$orgId}/bot/agents/{$id}");
    }

    /**
     * Conversa con el configurador para ajustar las instrucciones del agente. Solo propone: no guarda nada.
     *
     * @param array<string,mixed> $input mensajes [{rol, texto}], instrucciones?, orgId?
     * @return mixed
     */
    public function configurador(string $agentId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/bot/agents/{$agentId}/configurador", ['body' => $input]);
    }

    /**
     * Prueba el agente sin enviar nada al cliente: lo que respondería y qué herramientas usaría.
     *
     * @param array<string,mixed> $input mensajes [{rol, texto}], instrucciones?, orgId?
     * @return mixed
     */
    public function probador(string $agentId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/bot/agents/{$agentId}/probador", ['body' => $input]);
    }
}
