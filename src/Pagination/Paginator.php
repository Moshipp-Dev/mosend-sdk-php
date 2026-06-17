<?php

declare(strict_types=1);

namespace Mosend\Pagination;

/** Helpers de paginación por cursor opaco para los listados de la API. */
final class Paginator
{
    /**
     * Normaliza el payload de un listado a `data` + info de cursor. Acepta el
     * shape `{ data: [...], pageInfo: { endCursor, hasNextPage } }` o una lista
     * plana.
     *
     * @param mixed $payload
     * @return array{data:array<int,mixed>,endCursor:?string,hasNextPage:bool}
     */
    public static function page($payload): array
    {
        if (is_array($payload) && isset($payload['data']) && is_array($payload['data'])) {
            $info = is_array($payload['pageInfo'] ?? null) ? $payload['pageInfo'] : [];
            return [
                'data' => $payload['data'],
                'endCursor' => $info['endCursor'] ?? null,
                'hasNextPage' => (bool) ($info['hasNextPage'] ?? false),
            ];
        }
        if (is_array($payload)) {
            return ['data' => $payload, 'endCursor' => null, 'hasNextPage' => false];
        }
        return ['data' => [], 'endCursor' => null, 'hasNextPage' => false];
    }

    /**
     * Itera todas las páginas por cursor. `$fetch` recibe los params (con
     * `cursor` inyectado) y devuelve el payload crudo del listado.
     *
     * @param callable(array<string,mixed>):mixed $fetch
     * @param array<string,mixed> $params
     * @return \Generator<int,mixed>
     */
    public static function iterate(callable $fetch, array $params): \Generator
    {
        $cursor = null;
        $safety = 0;
        do {
            $p = $cursor === null ? $params : array_merge($params, ['cursor' => $cursor]);
            $page = self::page($fetch($p));
            foreach ($page['data'] as $item) {
                yield $item;
            }
            $cursor = ($page['hasNextPage'] && $page['endCursor']) ? $page['endCursor'] : null;
            if (++$safety > 10000) {
                throw new \RuntimeException('Paginator: límite de seguridad excedido (10000 páginas).');
            }
        } while ($cursor !== null);
    }
}
