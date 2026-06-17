<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/waba` — cuentas de WhatsApp Business. */
final class WabaResource extends Resource
{
    /** @return mixed */
    public function list(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/waba");
    }

    /** @return mixed */
    public function retrieve(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/waba/{$id}");
    }

    /** @param array<string,mixed> $input wabaId, phoneNumberId, accessToken, wabaName?, orgId? @return mixed */
    public function connectTestNumber(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/waba/connect-test-number", ['body' => $input]);
    }

    /** Archiva (soft-delete) la WABA. */
    public function archive(string $id, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('DELETE', "/organizations/{$orgId}/waba/{$id}");
    }

    /** @return mixed */
    public function restore(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/waba/{$id}/restore");
    }

    public function purge(string $id, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('DELETE', "/organizations/{$orgId}/waba/{$id}/purge");
    }
}
