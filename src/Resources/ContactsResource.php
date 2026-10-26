<?php

declare(strict_types=1);

namespace Mosend\Resources;

use Mosend\Pagination\Paginator;

/** `/organizations/{orgId}/contacts` — contactos, notas y acciones masivas. */
final class ContactsResource extends Resource
{
    /**
     * Filtros: q, channel ('whatsapp'|'web'|'all'), identifiedOnly, tagId, optInStatus, country,
     * language, createdSince, hasConversations, lastActivityDaysGt. Del CRM: duenoUserId (o
     * 'ninguno'), empresaId (o 'ninguna'), etapa (PROSPECTO|CLIENTE|RECURRENTE|INACTIVO),
     * campos (array clave => valor; viaja como JSON), compraEnDias, sinCompraDias, gastoMin,
     * puntajeMin.
     *
     * @param array<string,mixed> $query filtros + page?, pageSize?, orgId?
     * @return mixed payload paginado (page/pageSize)
     */
    public function list(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/contacts", ['query' => self::filterQuery($query)]);
    }

    /**
     * Todos los contactos que coinciden con los filtros (los mismos de `list`), sin
     * paginar. Devuelve `{ filas, truncado }`.
     *
     * @param array<string,mixed> $query filtros de `list`, orgId?
     * @return mixed
     */
    public function export(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/contacts/export", ['query' => self::filterQuery($query)]);
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

    /**
     * Importa contactos desde un CSV (multipart, campo `file`). `mapeo` dice a qué destino
     * va cada columna: phone, name, email, language, opt_in, empresa, empresa_nit, dueno,
     * etapa, ignorar o campo:<clave>. Usa `previewImport` para ver el mapeo sugerido.
     *
     * @param string|\CURLFile $file ruta del CSV o CURLFile
     * @param array<string,mixed> $options defaultCountry?, defaultOptIn? (bool), mapeo? (array columna => destino), orgId?
     * @return mixed resumen { total, created, updated, skipped, errors }
     */
    public function import($file, array $options = [])
    {
        $orgId = $this->requireOrgId($options['orgId'] ?? null);
        return $this->http->request('POST', "/organizations/{$orgId}/contacts/import", self::csvRequest($file, $options));
    }

    /**
     * Vista previa de un CSV: columnas, filas de muestra y el mapeo sugerido. No guarda nada.
     *
     * @param string|\CURLFile $file
     * @return mixed
     */
    public function previewImport($file, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/contacts/import/preview", [
            'multipart' => ['file' => $file],
        ]);
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

    /** @param array<string,mixed> $input body, pinned?, menciones? (userIds), adjuntos? (de uploadNoteAttachment), orgId? @return mixed */
    public function addNote(string $contactId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/contacts/{$contactId}/notes", ['body' => $input]);
    }

    /**
     * Sube un archivo (máx. 10 MB) para adjuntarlo a una nota. Devuelve
     * `{ mediaId, nombre, tipo, tamano }`, que se pasa en `adjuntos` al crear o editar la nota.
     *
     * @param string|\CURLFile $file
     * @return mixed
     */
    public function uploadNoteAttachment(string $contactId, $file, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/contacts/{$contactId}/notes/adjuntos", [
            'multipart' => ['file' => $file],
        ]);
    }

    /** @param array<string,mixed> $input body?, pinned?, menciones?, adjuntos?, orgId? @return mixed */
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

    /**
     * `campos` viaja como JSON en la query (`{"segmento":"VIP"}`).
     *
     * @param array<string,mixed> $query
     * @return array<string,mixed>
     */
    private static function filterQuery(array $query): array
    {
        if (isset($query['campos']) && is_array($query['campos'])) {
            $query['campos'] = json_encode($query['campos']);
        }
        return $query;
    }

    /**
     * Arma la petición multipart de importación (CSV + mapeo; opciones por query).
     *
     * @param string|\CURLFile $file
     * @param array<string,mixed> $options
     * @return array<string,mixed>
     */
    private static function csvRequest($file, array $options): array
    {
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
        return ['query' => $query, 'multipart' => $multipart];
    }
}
