<?php

declare(strict_types=1);

namespace Mosend\Resources;

use Mosend\Pagination\Paginator;

/** `/organizations/{orgId}/contacts` — contactos, notas y acciones masivas. */
final class ContactsResource extends Resource
{
    /**
     * @param array<string,mixed> $query q, tagId, channel, optInStatus, page, pageSize, orgId?
     * @return mixed payload paginado (page/pageSize)
     */
    public function list(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/contacts", ['query' => $query]);
    }

    /**
     * Itera TODOS los contactos. Paginación por offset (page/pageSize): avanza
     * la página hasta recibir una incompleta.
     *
     * @param array<string,mixed> $query
     * @return \Generator<int,mixed>
     */
    public function iterate(array $query = []): \Generator
    {
        $pageSize = (int) ($query['pageSize'] ?? 50);
        $page = (int) ($query['page'] ?? 1);
        do {
            $payload = $this->list(array_merge($query, ['page' => $page, 'pageSize' => $pageSize]));
            $items = Paginator::page($payload)['data'];
            foreach ($items as $item) {
                yield $item;
            }
            $page++;
        } while (count($items) >= $pageSize && count($items) > 0);
    }

    /** @param array<string,mixed> $input waId, name?, language?, attributes?, orgId? @return mixed */
    public function create(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/contacts", ['body' => $input]);
    }

    /** @return mixed */
    public function retrieve(string $contactId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/contacts/{$contactId}");
    }

    /** @param array<string,mixed> $input name?, language?, optInStatus?, attributes?, orgId? @return mixed */
    public function update(string $contactId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/contacts/{$contactId}", ['body' => $input]);
    }

    public function delete(string $contactId, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('DELETE', "/organizations/{$orgId}/contacts/{$contactId}");
    }

    /** @param array<int,array<string,mixed>> $contacts @return mixed */
    public function import(array $contacts, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/contacts/import", ['body' => ['contacts' => $contacts]]);
    }

    /** @param array<string,mixed> $input contactIds, tagId, orgId? @return mixed */
    public function bulkTag(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/contacts/bulk-tag", ['body' => $input]);
    }

    /** @param array<string,mixed> $input contactIds, orgId? @return mixed */
    public function bulkDelete(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/contacts/bulk-delete", ['body' => $input]);
    }

    /** @param array<string,mixed> $input contactIds, status, orgId? @return mixed */
    public function bulkSetOptInStatus(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/contacts/bulk-opt-in-status", ['body' => $input]);
    }

    /** @return mixed */
    public function listNotes(string $contactId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/contacts/{$contactId}/notes");
    }

    /** @param array<string,mixed> $input body, pinned?, orgId? @return mixed */
    public function addNote(string $contactId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/contacts/{$contactId}/notes", ['body' => $input]);
    }

    /** @param array<string,mixed> $input body?, pinned?, orgId? @return mixed */
    public function updateNote(string $contactId, string $noteId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/contacts/{$contactId}/notes/{$noteId}", ['body' => $input]);
    }

    public function deleteNote(string $contactId, string $noteId, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('DELETE', "/organizations/{$orgId}/contacts/{$contactId}/notes/{$noteId}");
    }

    /** @return mixed */
    public function listTasks(string $contactId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/contacts/{$contactId}/tasks");
    }
}
