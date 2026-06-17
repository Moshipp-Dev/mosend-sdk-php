<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/integrations` — catálogo e instalaciones. */
final class IntegrationsResource extends Resource
{
    /** @return mixed */
    public function list(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/integrations");
    }

    /** @return mixed */
    public function catalog(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/integrations/catalog");
    }

    /** @return mixed */
    public function catalogItem(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/integrations/catalog/{$id}");
    }

    /** @param array<string,mixed> $input slug, config?, orgId? @return mixed */
    public function install(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/integrations/install", ['body' => $input]);
    }

    /** @param array<string,mixed> $input config?, enabled?, orgId? @return mixed */
    public function update(string $id, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/integrations/catalog/{$id}", ['body' => $input]);
    }

    public function uninstall(string $id, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('DELETE', "/organizations/{$orgId}/integrations/catalog/{$id}");
    }
}
