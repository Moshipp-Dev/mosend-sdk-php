<?php

declare(strict_types=1);

namespace Mosend\Resources;

use Mosend\Pagination\Paginator;

/** `/organizations/{orgId}/conversations` — listado, detalle y acciones. */
final class ConversationsResource extends Resource
{
    /**
     * Una página de conversaciones.
     *
     * @param array<string,mixed> $query status, assigneeUserId, take, cursor, search, orgId?
     * @return array{data:array<int,mixed>,endCursor:?string,hasNextPage:bool}
     */
    public function list(array $query = []): array
    {
        return Paginator::page($this->rawList($query));
    }

    /**
     * Itera TODAS las conversaciones (maneja el cursor por vos).
     *
     * @param array<string,mixed> $query
     * @return \Generator<int,mixed>
     */
    public function iterate(array $query = []): \Generator
    {
        $self = $this;
        return Paginator::iterate(static function (array $params) use ($self) {
            return $self->rawList($params);
        }, $query);
    }

    /** @return mixed */
    public function retrieve(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/conversations/{$id}");
    }

    /**
     * @param array<string,mixed> $query
     * @return mixed payload crudo del listado
     */
    private function rawList(array $query)
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/conversations", ['query' => $query]);
    }
}
