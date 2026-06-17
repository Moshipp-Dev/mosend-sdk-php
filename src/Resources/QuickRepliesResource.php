<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/quick-replies` — respuestas rápidas. */
final class QuickRepliesResource extends Resource
{
    /** @return mixed */
    public function list(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/quick-replies");
    }

    /** @param array<string,mixed> $input shortcut, title, body, orgId? @return mixed */
    public function create(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/quick-replies", ['body' => $input]);
    }

    /** @param array<string,mixed> $input shortcut?, title?, body?, orgId? @return mixed */
    public function update(string $id, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/quick-replies/{$id}", ['body' => $input]);
    }

    public function delete(string $id, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('DELETE', "/organizations/{$orgId}/quick-replies/{$id}");
    }

    /** @return mixed */
    public function markUsed(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/quick-replies/{$id}/use");
    }
}
