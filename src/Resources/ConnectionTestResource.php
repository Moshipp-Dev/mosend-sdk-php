<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/phone-numbers/{id}/prueba-de-conexion` — prueba de ida y vuelta de un número. */
final class ConnectionTestResource extends Resource
{
    /**
     * Abre la prueba: devuelve el texto y el enlace para escribirle al número; al llegar, se responde solo.
     *
     * @return mixed
     */
    public function start(string $phoneNumberId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/phone-numbers/{$phoneNumberId}/prueba-de-conexion");
    }

    /**
     * Estado: esperando, recibido, respondido, fallo o vencida.
     *
     * @return mixed
     */
    public function status(string $phoneNumberId, string $codigo, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/phone-numbers/{$phoneNumberId}/prueba-de-conexion/{$codigo}");
    }
}
