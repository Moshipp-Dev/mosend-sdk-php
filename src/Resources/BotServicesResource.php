<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/bot/servicios` — catálogo de servicios que el bot puede citar. */
final class BotServicesResource extends Resource
{
    /**
     * Catálogo de servicios del bot.
     *
     * @return mixed
     */
    public function list(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/bot/servicios");
    }

    /**
     * Agrega un servicio al catálogo.
     *
     * @param array<string,mixed> $input categoria?, nombre?, descripcion?, notas?, moneda?, urlContratacion?, activo?, mostrarPrecio?, mostrarSinStock?, orden?, precios? [{periodo, valorCents}], orgId?
     * @return mixed
     */
    public function create(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/bot/servicios", ['body' => $input]);
    }

    /**
     * Edita un servicio del catálogo.
     *
     * @param array<string,mixed> $input mismos campos que create, orgId?
     * @return mixed
     */
    public function update(string $id, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/bot/servicios/{$id}", ['body' => $input]);
    }

    /**
     * Quita un servicio del catálogo.
     */
    public function delete(string $id, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('DELETE', "/organizations/{$orgId}/bot/servicios/{$id}");
    }
}
