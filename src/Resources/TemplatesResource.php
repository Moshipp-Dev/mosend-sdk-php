<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/templates` — plantillas de WhatsApp. */
final class TemplatesResource extends Resource
{
    /** @param array<string,mixed> $query status?, category?, language?, cursor?, limit?, orgId? @return mixed */
    public function list(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/templates", ['query' => $query]);
    }

    /** @return mixed */
    public function retrieve(string $templateId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/templates/{$templateId}");
    }

    /** @param array<string,mixed> $input wabaId, name, language, category, components, orgId? @return mixed */
    public function create(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/templates", ['body' => $input]);
    }

    /** @param array<string,mixed> $input components, orgId? @return mixed */
    public function update(string $templateId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/templates/{$templateId}", ['body' => $input]);
    }

    public function delete(string $templateId, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('DELETE', "/organizations/{$orgId}/templates/{$templateId}");
    }

    /** @return mixed */
    public function sync(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/templates/sync");
    }

    /**
     * Sube el media de cabecera (imagen/video/PDF) y devuelve `header_handle`.
     *
     * @param string|\CURLFile $file ruta del archivo o CURLFile
     * @return mixed
     */
    public function uploadHeaderMedia($file, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/templates/upload-header-media", [
            'multipart' => ['file' => $file],
        ]);
    }
}
