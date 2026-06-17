<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/tasks` — tareas sobre contactos. */
final class TasksResource extends Resource
{
    /** @param array<string,mixed> $query scope?, status?, contactId?, conversationId?, limit?, orgId? @return mixed */
    public function list(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/tasks", ['query' => $query]);
    }

    /** @return mixed */
    public function counts(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/tasks/counts");
    }

    /** @param array<string,mixed> $input contactId, title, dueAt, ..., orgId? @return mixed */
    public function create(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/tasks", ['body' => $input]);
    }

    /** @param array<string,mixed> $input @return mixed */
    public function update(string $taskId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/tasks/{$taskId}", ['body' => $input]);
    }

    /** @param array<string,mixed> $input completed, orgId? @return mixed */
    public function setCompleted(string $taskId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/tasks/{$taskId}/complete", ['body' => $input]);
    }

    /** @return mixed */
    public function claim(string $taskId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('PATCH', "/organizations/{$orgId}/tasks/{$taskId}/claim");
    }

    public function delete(string $taskId, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('DELETE', "/organizations/{$orgId}/tasks/{$taskId}");
    }
}
