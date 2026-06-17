<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/bot/knowledge` — base de conocimiento (RAG). */
final class KnowledgeResource extends Resource
{
    /** @return mixed */
    public function list(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/bot/knowledge");
    }

    /** @return mixed */
    public function retrieve(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/bot/knowledge/{$id}");
    }

    /**
     * Sube un documento (multipart). $input: file (ruta|CURLFile), title?, tags? (array|string).
     *
     * @param array<string,mixed> $input
     * @return mixed
     */
    public function upload(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        $multipart = ['file' => $input['file']];
        if (isset($input['title'])) {
            $multipart['title'] = (string) $input['title'];
        }
        if (isset($input['tags'])) {
            $multipart['tags'] = is_array($input['tags']) ? implode(',', $input['tags']) : (string) $input['tags'];
        }
        return $this->http->request('POST', "/organizations/{$orgId}/bot/knowledge", ['multipart' => $multipart]);
    }

    /** @param array<string,mixed> $input title, orgId? @return mixed */
    public function updateTitle(string $id, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/bot/knowledge/{$id}/title", ['body' => $input]);
    }

    /** @param array<string,mixed> $input tags, orgId? @return mixed */
    public function updateTags(string $id, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/bot/knowledge/{$id}/tags", ['body' => $input]);
    }

    /** @return mixed */
    public function reprocess(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/bot/knowledge/{$id}/reprocess");
    }

    public function delete(string $id, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('DELETE', "/organizations/{$orgId}/bot/knowledge/{$id}");
    }
}
