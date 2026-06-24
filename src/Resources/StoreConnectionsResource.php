<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/store-connections` — conexiones de tienda (WooCommerce/Shopify) y mapeos evento→plantilla. */
final class StoreConnectionsResource extends Resource
{
    /** @return mixed */
    public function list(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/store-connections");
    }

    /** @param array<string,mixed> $input platform, name, phoneNumberId, storeDomain?, orgId? @return mixed */
    public function create(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/store-connections", ['body' => $input]);
    }

    /** @param array<string,mixed> $input name?, phoneNumberId?, storeDomain?, status?(ACTIVE|PAUSED), orgId? @return mixed */
    public function update(string $id, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/store-connections/{$id}", ['body' => $input]);
    }

    /** @return mixed */
    public function delete(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('DELETE', "/organizations/{$orgId}/store-connections/{$id}");
    }

    /** Rota el ingestSecret de la conexión. @return mixed */
    public function rotateSecret(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/store-connections/{$id}/rotate-secret");
    }

    /** Lista los mapeos evento→plantilla de la conexión. @return mixed */
    public function listMappings(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/store-connections/{$id}/mappings");
    }

    /** @param array<string,mixed> $input eventType, enabled?, templateId?, languageCode?, variableMap?, delayMinutes?, orgId? @return mixed */
    public function upsertMapping(string $id, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/store-connections/{$id}/mappings", ['body' => $input]);
    }

    /** Log de eventos recibidos de la tienda. @param array<string,mixed> $query limit?, orgId? @return mixed */
    public function listEvents(string $id, array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/store-connections/{$id}/events", ['query' => $query]);
    }
}
