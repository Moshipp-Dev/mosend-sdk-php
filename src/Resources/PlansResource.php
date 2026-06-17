<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/plans` (público) + cambios de plan por organización. */
final class PlansResource extends Resource
{
    /** @return mixed */
    public function list()
    {
        return $this->http->request('GET', '/plans');
    }

    /** @return mixed */
    public function retrieve(string $slug)
    {
        return $this->http->request('GET', "/plans/{$slug}");
    }

    /** @param array<string,mixed> $query @return mixed */
    public function quote(string $slug, array $query = [])
    {
        return $this->http->request('GET', "/plans/quote/{$slug}", ['query' => $query]);
    }

    /** @param array<string,mixed> $input toPlanSlug, couponCode?, orgId? @return mixed */
    public function previewChange(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/plans/organizations/{$orgId}/preview-change", ['body' => $input]);
    }

    /** @return mixed */
    public function cancelSubscription(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/plans/organizations/{$orgId}/cancel-subscription");
    }

    /** @param array<string,mixed> $input toPlanSlug, couponCode?, extraSeats?, reason?, orgId? @return mixed */
    public function change(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/plans/organizations/{$orgId}/plan", ['body' => $input]);
    }
}
