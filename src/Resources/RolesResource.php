<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/roles` — roles y permisos. */
final class RolesResource extends Resource
{
    /** @return mixed */
    public function list(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/roles");
    }

    /** @param array<string,mixed> $input name, description?, key?, permissions?, orgId? @return mixed */
    public function create(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/roles", ['body' => $input]);
    }

    /** @param array<string,mixed> $input name?, description?, orgId? @return mixed */
    public function update(string $id, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/roles/{$id}", ['body' => $input]);
    }

    /** @param array<string,mixed> $input permissions, orgId? @return mixed */
    public function setPermissions(string $id, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PUT', "/organizations/{$orgId}/roles/{$id}/permissions", ['body' => $input]);
    }

    public function delete(string $id, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('DELETE', "/organizations/{$orgId}/roles/{$id}");
    }
}
