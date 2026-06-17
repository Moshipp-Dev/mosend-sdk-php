<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/invitations` (+ accept público). */
final class InvitationsResource extends Resource
{
    /** @return mixed */
    public function list(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/invitations");
    }

    /** @param array<string,mixed> $input email, roleId, orgId? @return mixed */
    public function create(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/invitations", ['body' => $input]);
    }

    public function revoke(string $id, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('DELETE', "/organizations/{$orgId}/invitations/{$id}");
    }

    /** @return mixed */
    public function resend(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/invitations/{$id}/resend");
    }

    /** @param array<string,mixed> $input token @return mixed */
    public function accept(array $input)
    {
        return $this->http->request('POST', '/invitations/accept', ['body' => $input]);
    }
}
