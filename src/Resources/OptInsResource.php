<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/contacts/{contactId}/opt-ins` — consentimiento. */
final class OptInsResource extends Resource
{
    /** @return mixed */
    public function list(string $contactId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/contacts/{$contactId}/opt-ins");
    }

    /** @param array<string,mixed> $input type ('IN'|'OUT'), source?, channel?, payload?, orgId? @return mixed */
    public function create(string $contactId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/contacts/{$contactId}/opt-ins", ['body' => $input]);
    }
}
