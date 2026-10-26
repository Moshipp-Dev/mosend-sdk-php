<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/admin/wabas` — uso y rentabilidad por WABA. Solo staff de Mosend. */
final class WabaUsageResource extends Resource
{
    /**
     * Comparativa de cada WABA (mensajes, plantillas, fallidos, costo de Meta, IA) contra el período anterior.
     *
     * @param array<string,mixed> $query days?, desde?, hasta?
     * @return mixed
     */
    public function list(array $query = [])
    {
        return $this->http->request('GET', '/admin/wabas/uso', ['query' => $query]);
    }

    /**
     * Detalle de una WABA: mensajes y costo por día, plantillas que más cuestan, errores frecuentes.
     *
     * @param array<string,mixed> $query days?, desde?, hasta?
     * @return mixed
     */
    public function detail(string $wabaId, array $query = [])
    {
        return $this->http->request('GET', "/admin/wabas/{$wabaId}/detalle", ['query' => $query]);
    }
}
