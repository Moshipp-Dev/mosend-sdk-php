<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/documents` — repositorio de documentos y carpetas de la org. */
final class DocumentsResource extends Resource
{
    // ─── Carpetas ────────────────────────────────────────────────────────────

    /** @return mixed */
    public function listFolders(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/documents/folders");
    }

    /** @param array<string,mixed> $input name, parentId?, visibility?(ORG|PRIVATE), orgId? @return mixed */
    public function createFolder(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/documents/folders", ['body' => $input]);
    }

    /** @param array<string,mixed> $input visibility(ORG|PRIVATE), orgId? @return mixed */
    public function setFolderVisibility(string $folderId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/documents/folders/{$folderId}/visibility", ['body' => $input]);
    }

    /** @param array<string,mixed> $input name, orgId? @return mixed */
    public function renameFolder(string $folderId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/documents/folders/{$folderId}", ['body' => $input]);
    }

    /** @return mixed */
    public function deleteFolder(string $folderId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('DELETE', "/organizations/{$orgId}/documents/folders/{$folderId}");
    }

    // ─── Documentos ──────────────────────────────────────────────────────────

    /** @param array<string,mixed> $query folderId?, q?, type?, orgId? @return mixed */
    public function listDocuments(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/documents", ['query' => $query]);
    }

    /** @return mixed */
    public function listTrash(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/documents/trash");
    }

    /** Uso de almacenamiento del plan. @return mixed */
    public function storageUsage(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/documents/storage");
    }

    /** Guarda en Documentos el adjunto de un mensaje entrante. @return mixed */
    public function saveFromMessage(string $messageId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/documents/from-message", [
            'body' => ['messageId' => $messageId],
        ]);
    }

    /**
     * Sube un documento (campo `file` multipart). Los campos `name`/`visibility`
     * van como parte del multipart; `folderId` va como query.
     *
     * @param string|\CURLFile $file
     * @param array<string,mixed> $options name?, visibility?(ORG|PRIVATE), folderId?, orgId?
     * @return mixed
     */
    public function upload($file, array $options = [])
    {
        $orgId = $this->requireOrgId($options['orgId'] ?? null);
        $query = [];
        if (isset($options['folderId'])) {
            $query['folderId'] = $options['folderId'];
        }
        $multipart = ['file' => $file];
        if (isset($options['name'])) {
            $multipart['name'] = $options['name'];
        }
        if (isset($options['visibility'])) {
            $multipart['visibility'] = $options['visibility'];
        }
        return $this->http->request('POST', "/organizations/{$orgId}/documents", [
            'query' => $query,
            'multipart' => $multipart,
        ]);
    }

    /** @param array<string,mixed> $input visibility(ORG|PRIVATE), orgId? @return mixed */
    public function setDocumentVisibility(string $docId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/documents/{$docId}/visibility", ['body' => $input]);
    }

    /** URL firmada para abrir/descargar el documento. @return mixed */
    public function viewUrl(string $docId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/documents/{$docId}/view");
    }

    /** @param array<string,mixed> $input name, orgId? @return mixed */
    public function renameDocument(string $docId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/documents/{$docId}", ['body' => $input]);
    }

    /** @param array<string,mixed> $input folderId(string|null para la raíz), orgId? @return mixed */
    public function moveDocument(string $docId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/documents/{$docId}/move", ['body' => $input]);
    }

    /** @param array<string,mixed> $input conversationId, caption?, orgId? @return mixed */
    public function send(string $docId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/documents/{$docId}/send", ['body' => $input]);
    }

    /** Restaura un documento de la papelera. @return mixed */
    public function restore(string $docId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/documents/{$docId}/restore");
    }

    /** Manda un documento a la papelera (borrado suave). @return mixed */
    public function delete(string $docId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('DELETE', "/organizations/{$orgId}/documents/{$docId}");
    }

    /** Elimina definitivamente un documento. @return mixed */
    public function purge(string $docId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('DELETE', "/organizations/{$orgId}/documents/{$docId}/purge");
    }
}
