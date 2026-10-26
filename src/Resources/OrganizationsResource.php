<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations` — organizaciones. */
final class OrganizationsResource extends Resource
{
    /** @return mixed */
    public function list()
    {
        return $this->http->request('GET', '/organizations');
    }

    /** @param array<string,mixed> $input name, slug, billingEmail, country?, currency?, timezone? @return mixed */
    public function create(array $input)
    {
        return $this->http->request('POST', '/organizations', ['body' => $input]);
    }

    /** @return mixed */
    public function suggestSlug(string $name)
    {
        return $this->http->request('GET', '/organizations/slug-suggest', ['query' => ['name' => $name]]);
    }

    /** @return mixed */
    public function isSlugAvailable(string $slug)
    {
        return $this->http->request('GET', '/organizations/slug-available', ['query' => ['slug' => $slug]]);
    }

    /** @return mixed */
    public function retrieve(string $id)
    {
        return $this->http->request('GET', "/organizations/{$id}");
    }

    /** @param array<string,mixed> $input @return mixed */
    public function update(string $id, array $input)
    {
        return $this->http->request('PATCH', "/organizations/{$id}", ['body' => $input]);
    }

    /**
     * Cupo del usuario para crear organizaciones.
     * @return mixed
     */
    public function quota()
    {
        return $this->http->request('GET', '/organizations/quota');
    }

    /**
     * Motivo de la suspensión y facturas pendientes de pago.
     * @return mixed
     */
    public function suspension(string $id)
    {
        return $this->http->request('GET', "/organizations/{$id}/suspension");
    }

    /**
     * Estado del grupo de facturación de la organización.
     *
     * @return mixed
     */
    public function billingGroup(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/billing-group");
    }

    /**
     * Compartir facturación: enlaza la org como hija de una matriz (solo owner).
     *
     * @param array<string,mixed> $input parentOrgId, orgId?
     * @return mixed
     */
    public function linkBillingGroup(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/billing-group/link", ['body' => $input]);
    }

    /**
     * Independiza la org de su matriz; pasa a plan free (solo owner).
     *
     * @return mixed
     */
    public function unlinkBillingGroup(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/billing-group/unlink");
    }

    /**
     * Tope de gasto de IA de una organización del grupo (solo la matriz, solo el dueño).
     *
     * @param array<string,mixed> $input dailyUsd?, monthlyUsd?, orgId?
     * @return mixed
     */
    public function setAiLimits(string $targetOrgId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/billing-group/{$targetOrgId}/ai-limits", ['body' => $input]);
    }
}
