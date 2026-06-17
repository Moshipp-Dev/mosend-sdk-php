<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/memberships` — miembros de la organización. */
final class MembershipsResource extends Resource
{
    /** @return mixed */
    public function list(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/memberships");
    }

    /** @return mixed */
    public function me(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/memberships/me");
    }

    /** @param array<string,mixed> $input roleId, orgId? @return mixed */
    public function setRole(string $id, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/memberships/{$id}", ['body' => $input]);
    }

    /** @param array<string,mixed> $input wabaIds, orgId? @return mixed */
    public function setWabaScope(string $id, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PUT', "/organizations/{$orgId}/memberships/{$id}/waba-scope", ['body' => $input]);
    }

    public function remove(string $id, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('DELETE', "/organizations/{$orgId}/memberships/{$id}");
    }
}
