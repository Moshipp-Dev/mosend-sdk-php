<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/tags` — etiquetas de conversaciones/contactos. */
final class TagsResource extends Resource
{
    /** @return mixed */
    public function list(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/tags");
    }

    /** @param array<string,mixed> $input name, color?, orgId? @return mixed */
    public function create(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/tags", ['body' => $input]);
    }

    public function delete(string $tagId, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('DELETE', "/organizations/{$orgId}/tags/{$tagId}");
    }
}
