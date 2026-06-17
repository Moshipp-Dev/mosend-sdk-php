<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/broadcasts` — difusiones masivas. */
final class BroadcastsResource extends Resource
{
    /** @param array<string,mixed> $query @return mixed */
    public function list(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/broadcasts", ['query' => $query]);
    }

    /** @return mixed */
    public function retrieve(string $broadcastId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/broadcasts/{$broadcastId}");
    }

    /** @param array<string,mixed> $input name, phoneNumberId, templateId, templateLanguage, listId?, contactIds?, templateVariables?, scheduledAt?, orgId? @return mixed */
    public function create(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/broadcasts", ['body' => $input]);
    }

    /** @return mixed */
    public function send(string $broadcastId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/broadcasts/{$broadcastId}/send");
    }

    /** @return mixed */
    public function cancel(string $broadcastId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/broadcasts/{$broadcastId}/cancel");
    }

    /** @param array<string,mixed> $query filter?, cursor?, limit?, orgId? @return mixed */
    public function recipients(string $broadcastId, array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/broadcasts/{$broadcastId}/recipients", ['query' => $query]);
    }
}
