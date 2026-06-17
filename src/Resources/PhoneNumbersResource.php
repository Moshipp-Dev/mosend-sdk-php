<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/phone-numbers` — números y registro. */
final class PhoneNumbersResource extends Resource
{
    /** @return mixed */
    public function list(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/phone-numbers");
    }

    /** @return mixed */
    public function retrieve(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/phone-numbers/{$id}");
    }

    /** @param array<string,mixed> $input wabaId, cc, phoneNumber, verifiedName, orgId? @return mixed */
    public function create(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/phone-numbers", ['body' => $input]);
    }

    /** @return mixed */
    public function sync(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/phone-numbers/sync");
    }

    public function delete(string $id, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('DELETE', "/organizations/{$orgId}/phone-numbers/{$id}");
    }

    /** @return mixed */
    public function restore(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/phone-numbers/{$id}/restore");
    }

    public function purge(string $id, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('DELETE', "/organizations/{$orgId}/phone-numbers/{$id}/purge");
    }

    /** @param array<string,mixed> $input method ('SMS'|'VOICE'), language?, orgId? */
    public function requestRegistrationCode(string $id, array $input): void
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        $this->http->request('POST', "/organizations/{$orgId}/phone-numbers/{$id}/registration/request-code", ['body' => $input]);
    }

    /** @param array<string,mixed> $input code, orgId? */
    public function verifyRegistrationCode(string $id, array $input): void
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        $this->http->request('POST', "/organizations/{$orgId}/phone-numbers/{$id}/registration/verify-code", ['body' => $input]);
    }

    /** @param array<string,mixed> $input pin, orgId? */
    public function register(string $id, array $input): void
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        $this->http->request('POST', "/organizations/{$orgId}/phone-numbers/{$id}/registration/register", ['body' => $input]);
    }

    public function deregister(string $id, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('POST', "/organizations/{$orgId}/phone-numbers/{$id}/registration/deregister");
    }
}
