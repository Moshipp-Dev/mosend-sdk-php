<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/messages/{messageId}/reactions` — reacciones emoji. */
final class ReactionsResource extends Resource
{
    /** @param array<string,mixed> $input emoji, orgId? @return mixed */
    public function set(string $messageId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PUT', "/organizations/{$orgId}/messages/{$messageId}/reactions", ['body' => $input]);
    }

    public function remove(string $messageId, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('DELETE', "/organizations/{$orgId}/messages/{$messageId}/reactions");
    }
}
