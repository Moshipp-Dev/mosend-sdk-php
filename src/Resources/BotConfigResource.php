<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/bot/config` — config del bot por número. */
final class BotConfigResource extends Resource
{
    /** @return mixed */
    public function list(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/bot/config");
    }

    /** @return mixed */
    public function retrieve(string $phoneId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/bot/config/{$phoneId}");
    }

    /** @param array<string,mixed> $input mode?, aiSystemPrompt?, aiModel?, ..., orgId? @return mixed */
    public function upsert(string $phoneId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PUT', "/organizations/{$orgId}/bot/config/{$phoneId}", ['body' => $input]);
    }

    /** @return mixed */
    public function toggle(string $phoneId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('PATCH', "/organizations/{$orgId}/bot/config/{$phoneId}/toggle");
    }
}
