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

    /**
     * Qué buscó el bot en la memoria: consultas, tasa de respuesta, preguntas sin respuesta, uso por agente y documento.
     *
     * @param array<string,mixed> $query dias?, desde?, hasta?, orgId?
     * @return mixed
     */
    public function insights(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/bot/knowledge/insights", ['query' => $query]);
    }

    /**
     * Búsqueda semántica (RAG): el mismo retrieval que usa el bot IA.
     *
     * @param array<string,mixed> $input query, topK?, tags?, minSimilarity?, agentId?, orgId?
     * @return mixed
     */
    public function search(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/bot/knowledge/search", ['body' => $input]);
    }

    /**
     * Crea una nota escrita en el panel; se procesa como un documento.
     *
     * @param array<string,mixed> $input title?, content?, tags?, resolvesQuestionKey?, agentIds?, compartido?, orgId?
     * @return mixed
     */
    public function createNote(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/bot/knowledge/notes", ['body' => $input]);
    }

    /**
     * Edita una nota; si cambia el texto se vuelve a procesar.
     *
     * @param array<string,mixed> $input title?, content?, tags?, resolvesQuestionKey?, agentIds?, compartido?, orgId?
     * @return mixed
     */
    public function updateNote(string $id, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/bot/knowledge/notes/{$id}", ['body' => $input]);
    }

    /**
     * Importa una página web pública; con syncEveryHours se vuelve a leer periódicamente.
     *
     * @param array<string,mixed> $input url, title?, tags?, syncEveryHours?, agentIds?, compartido?, orgId?
     * @return mixed
     */
    public function createFromUrl(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/bot/knowledge/url", ['body' => $input]);
    }

    /**
     * Qué agentes leen el documento, o si lo leen todos (compartido).
     *
     * @param array<string,mixed> $input agentIds, compartido, orgId?
     * @return mixed
     */
    public function updateAgents(string $id, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/bot/knowledge/{$id}/agentes", ['body' => $input]);
    }
}
