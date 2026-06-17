<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/contact-lists` — listas de contactos y membresías. */
final class ContactListsResource extends Resource
{
    /** @param array<string,mixed> $query @return mixed */
    public function list(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/contact-lists", ['query' => $query]);
    }

    /** @param array<string,mixed> $input name, description?, color?, orgId? @return mixed */
    public function create(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/contact-lists", ['body' => $input]);
    }

    /** @return mixed */
    public function retrieve(string $listId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/contact-lists/{$listId}");
    }

    /** @param array<string,mixed> $input @return mixed */
    public function update(string $listId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/contact-lists/{$listId}", ['body' => $input]);
    }

    public function delete(string $listId, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('DELETE', "/organizations/{$orgId}/contact-lists/{$listId}");
    }

    /** @return mixed */
    public function listMembers(string $listId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/contact-lists/{$listId}/members");
    }

    /** @param array<string,mixed> $input contactIds, orgId? @return mixed */
    public function addMembers(string $listId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/contact-lists/{$listId}/members", ['body' => $input]);
    }

    /** @return mixed */
    public function removeMember(string $listId, string $contactId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('DELETE', "/organizations/{$orgId}/contact-lists/{$listId}/members/{$contactId}");
    }

    /** @param array<string,mixed> $input tagIds, orgId? @return mixed */
    public function addByTag(string $listId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/contact-lists/{$listId}/add-by-tag", ['body' => $input]);
    }
}
