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

    /**
     * `smartFilter` la vuelve lista inteligente: guarda el filtro de contactos (etapa,
     * duenoUserId, empresaId, tagId, campos, compraEnDias, sinCompraDias, gastoMin,
     * puntajeMin, lastActivityDaysGt…) y sus miembros se calculan al usarla.
     *
     * @param array<string,mixed> $input name, description?, color?, smartFilter?, orgId?
     * @return mixed
     */
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

    /** @param array<string,mixed> $input name?, description? (null borra), color?, smartFilter? (null = deja de ser inteligente), orgId? @return mixed */
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

    /**
     * @param array<string,mixed>|string|null $query page?, pageSize? (máx. 200), orgId? — un string
     *   se toma como orgId (firma anterior).
     * @return mixed
     */
    public function listMembers(string $listId, $query = [])
    {
        $query = is_array($query) ? $query : ['orgId' => $query];
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/contact-lists/{$listId}/members", ['query' => $query]);
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

    /** Quita varios contactos (máx. 1000 por petición). @param array<string,mixed> $input contactIds, orgId? @return mixed */
    public function removeMembers(string $listId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('DELETE', "/organizations/{$orgId}/contact-lists/{$listId}/members", ['body' => $input]);
    }

    /**
     * Agrega los contactos que coinciden con el filtro.
     *
     * @param array<string,mixed> $input q?, channel?, identifiedOnly?, tagId?, optInStatus?, country?, language?,
     *   hasConversations?, lastActivityDaysGt?, orgId?
     * @return mixed { matched, added }
     */
    public function addByFilter(string $listId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/contact-lists/{$listId}/add-by-filter", ['body' => $input]);
    }

    /** Quita los miembros que coinciden con el filtro (mismos campos que addByFilter). @param array<string,mixed> $input @return mixed */
    public function removeByFilter(string $listId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/contact-lists/{$listId}/remove-by-filter", ['body' => $input]);
    }

    /** Vacía la lista: quita todos sus miembros. La lista se conserva. @return mixed */
    public function clear(string $listId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/contact-lists/{$listId}/clear");
    }

    /**
     * Importa un CSV de contactos y los deja dentro de esta lista (multipart, campo `file`).
     * Mismo `mapeo` que `ContactsResource::import`.
     *
     * @param string|\CURLFile $file
     * @param array<string,mixed> $options defaultCountry?, defaultOptIn? (bool), mapeo? (array columna => destino), orgId?
     * @return mixed
     */
    public function import(string $listId, $file, array $options = [])
    {
        $orgId = $this->requireOrgId($options['orgId'] ?? null);
        $query = [];
        if (isset($options['defaultCountry'])) {
            $query['defaultCountry'] = $options['defaultCountry'];
        }
        if (isset($options['defaultOptIn'])) {
            $query['defaultOptIn'] = $options['defaultOptIn'] ? 'true' : 'false';
        }
        $multipart = ['file' => $file];
        if (isset($options['mapeo'])) {
            $multipart['mapeo'] = is_string($options['mapeo']) ? $options['mapeo'] : (string) json_encode($options['mapeo']);
        }
        return $this->http->request('POST', "/organizations/{$orgId}/contact-lists/{$listId}/import", [
            'query' => $query,
            'multipart' => $multipart,
        ]);
    }
}
