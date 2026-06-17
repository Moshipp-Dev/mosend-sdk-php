<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/whatsapp-links` — links wa.me con tracking. */
final class WhatsappLinksResource extends Resource
{
    /** @param array<string,mixed> $query @return mixed */
    public function list(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/whatsapp-links", ['query' => $query]);
    }

    /** @param array<string,mixed> $input name, phoneNumberId, prefilledMessage?, campaignTag?, metadata?, orgId? @return mixed */
    public function create(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/whatsapp-links", ['body' => $input]);
    }

    /** @return mixed */
    public function retrieve(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/whatsapp-links/{$id}");
    }

    /** @param array<string,mixed> $input @return mixed */
    public function update(string $id, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/whatsapp-links/{$id}", ['body' => $input]);
    }

    public function delete(string $id, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('DELETE', "/organizations/{$orgId}/whatsapp-links/{$id}");
    }

    /** @return mixed */
    public function stats(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/whatsapp-links/{$id}/stats");
    }

    /** @return mixed */
    public function qr(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/whatsapp-links/{$id}/qr");
    }
}
