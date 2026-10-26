<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/bot/resumen` y `/bot/salud` — cómo va el bot y qué se está rompiendo. */
final class BotSummaryResource extends Resource
{
    /**
     * Serie diaria de quién atendió, totales del período y del anterior, acciones y cierres.
     *
     * @param array<string,mixed> $query dias?, desde?, hasta?, orgId?
     * @return mixed
     */
    public function summary(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/bot/resumen", ['query' => $query]);
    }

    /**
     * En qué se fue la IA un día (cortado en la zona horaria de la org).
     *
     * @param array<string,mixed> $query fecha (YYYY-MM-DD), orgId?
     * @return mixed
     */
    public function daySpend(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/bot/resumen/dia", ['query' => $query]);
    }

    /**
     * Revisión de salud del bot: problemas detectados y qué hacer con cada uno.
     *
     * @param array<string,mixed> $query dias?, orgId?
     * @return mixed
     */
    public function health(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/bot/salud", ['query' => $query]);
    }
}
