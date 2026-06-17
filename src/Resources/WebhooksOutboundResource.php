<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/webhooks-outbound` — webhooks salientes. */
final class WebhooksOutboundResource extends Resource
{
    /** @return mixed */
    public function list(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/webhooks-outbound");
    }

    /** @param array<string,mixed> $input url, events, format?, ..., orgId? @return mixed */
    public function create(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/webhooks-outbound", ['body' => $input]);
    }

    /** @param array<string,mixed> $input @return mixed */
    public function update(string $id, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/webhooks-outbound/{$id}", ['body' => $input]);
    }

    /** @return mixed */
    public function getSecret(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/webhooks-outbound/{$id}/secret");
    }

    public function delete(string $id, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('DELETE', "/organizations/{$orgId}/webhooks-outbound/{$id}");
    }

    /** @param array<string,mixed> $query cursor?, limit?, orgId? @return mixed */
    public function deliveries(string $id, array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/webhooks-outbound/{$id}/deliveries", ['query' => $query]);
    }
}
