<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/bot/ai-providers` — BYOK de proveedores de IA. */
final class OrgAiProvidersResource extends Resource
{
    /** @return mixed */
    public function list(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/bot/ai-providers");
    }

    /** @return mixed */
    public function effective(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/bot/ai-providers/effective");
    }

    /** @param array<string,mixed> $input apiKey?, enabled?, defaultModel?, orgId? @return mixed */
    public function upsert(string $provider, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PUT', "/organizations/{$orgId}/bot/ai-providers/{$provider}", ['body' => $input]);
    }

    public function delete(string $provider, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('DELETE', "/organizations/{$orgId}/bot/ai-providers/{$provider}");
    }

    /** @return mixed */
    public function test(string $provider, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/bot/ai-providers/{$provider}/test");
    }
}
