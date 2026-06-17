<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/api-keys` — claves de API. */
final class ApiKeysResource extends Resource
{
    /** @return mixed */
    public function list(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/api-keys");
    }

    /** @param array<string,mixed> $input name, scopes?, phoneNumberIds?, orgId? @return mixed */
    public function create(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/api-keys", ['body' => $input]);
    }

    /** @param array<string,mixed> $input name?, scopes?, phoneNumberIds?, orgId? @return mixed */
    public function update(string $id, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/api-keys/{$id}", ['body' => $input]);
    }

    public function revoke(string $id, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('DELETE', "/organizations/{$orgId}/api-keys/{$id}");
    }
}
