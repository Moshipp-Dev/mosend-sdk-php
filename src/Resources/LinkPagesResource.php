<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/link-pages` — páginas de enlaces (bio / link-in-bio) y sus ítems. */
final class LinkPagesResource extends Resource
{
    /** @return mixed */
    public function list(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/link-pages");
    }

    /** Páginas archivadas (para restaurar). @return mixed */
    public function listArchived(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/link-pages/archived");
    }

    /** @param array<string,mixed> $input handle, displayName, bio?, theme?, orgId? @return mixed */
    public function create(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/link-pages", ['body' => $input]);
    }

    /** @return mixed */
    public function retrieve(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/link-pages/{$id}");
    }

    /** @param array<string,mixed> $input handle?, displayName?, bio?, theme?, isPublished?, coverMediaId?, avatarMediaId?, orgId? @return mixed */
    public function update(string $id, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/link-pages/{$id}", ['body' => $input]);
    }

    /** Toggle archivado de la página (soft-delete reversible). @return mixed */
    public function archive(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('DELETE', "/organizations/{$orgId}/link-pages/{$id}");
    }

    /** Restaura una página archivada (la republica). @return mixed */
    public function restore(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/link-pages/{$id}/restore");
    }

    /**
     * Sube la portada de la página (campo `file` multipart).
     *
     * @param string|\CURLFile $file
     * @return mixed
     */
    public function uploadCover(string $id, $file, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/link-pages/{$id}/cover", [
            'multipart' => ['file' => $file],
        ]);
    }

    /**
     * Sube la foto de perfil de la página (campo `file` multipart).
     *
     * @param string|\CURLFile $file
     * @return mixed
     */
    public function uploadAvatar(string $id, $file, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/link-pages/{$id}/avatar", [
            'multipart' => ['file' => $file],
        ]);
    }

    // ─── Ítems ───────────────────────────────────────────────────────────────

    /** @param array<string,mixed> $input type, title, subtitle?, icon?, config, orgId? @return mixed */
    public function addItem(string $id, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/link-pages/{$id}/items", ['body' => $input]);
    }

    /** @param string[] $itemIds ids en el nuevo orden @return mixed */
    public function reorderItems(string $id, array $itemIds, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('PATCH', "/organizations/{$orgId}/link-pages/{$id}/items/reorder", [
            'body' => ['itemIds' => $itemIds],
        ]);
    }

    /** @param array<string,mixed> $input title?, subtitle?, icon?, config?, isActive?, orgId? @return mixed */
    public function updateItem(string $id, string $itemId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/link-pages/{$id}/items/{$itemId}", ['body' => $input]);
    }

    /** @return mixed */
    public function deleteItem(string $id, string $itemId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('DELETE', "/organizations/{$orgId}/link-pages/{$id}/items/{$itemId}");
    }
}
