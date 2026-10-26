<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/ad-campaigns` — anuncios Click-to-WhatsApp que han traído conversaciones. */
final class AdCampaignsResource extends Resource
{
    /**
     * Anuncios que han traído conversaciones, con lo que han vendido.
     *
     * @return mixed
     */
    public function list(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/ad-campaigns");
    }

    /**
     * Dice qué significa un anuncio: qué vende, quién atiende, qué etiqueta.
     *
     * @param array<string,mixed> $input queOfrece?, productIds?, botAgentId?, tagId?, mensajeInicial?, activo?, orgId?
     * @return mixed
     */
    public function update(string $id, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/ad-campaigns/{$id}", ['body' => $input]);
    }

    /**
     * Quiénes llegaron por el anuncio y siguen sin comprar, con su ventana gratis de Meta.
     *
     * @return mixed
     */
    public function porSeguir(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/ad-campaigns/{$id}/por-seguir");
    }
}
