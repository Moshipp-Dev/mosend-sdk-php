<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/billing` — periodos, uso, cupones. */
final class BillingResource extends Resource
{
    /** @return mixed */
    public function periods(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/billing/periods");
    }

    /** @return mixed */
    public function usage(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/billing/usage");
    }

    /** @return mixed */
    public function estimatedNextCharge(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/billing/estimated-next-charge");
    }

    /** @param array<string,mixed> $input code, planSlug?, orgId? @return mixed */
    public function validateCoupon(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/billing/coupons/validate", ['body' => $input]);
    }

    /** @param array<string,mixed> $input code, planSlug?, orgId? @return mixed */
    public function redeemCoupon(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/billing/coupons/redeem", ['body' => $input]);
    }
}
