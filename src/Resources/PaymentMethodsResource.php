<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/billing/payment-methods` — medios de pago. */
final class PaymentMethodsResource extends Resource
{
    /** @return mixed */
    public function list(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/billing/payment-methods");
    }

    /** @param array<string,mixed> $input token, email, firstName?, lastName?, orgId? @return mixed */
    public function add(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/billing/payment-methods", ['body' => $input]);
    }

    public function delete(string $id, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('DELETE', "/organizations/{$orgId}/billing/payment-methods/{$id}");
    }

    /** @return mixed */
    public function setDefault(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/billing/payment-methods/{$id}/default");
    }

    /** @return mixed */
    public function preferences(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/billing/preferences");
    }

    /** @param array<string,mixed> $input enabled, methodId?, orgId? @return mixed */
    public function setAutoPay(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/billing/auto-pay", ['body' => $input]);
    }
}
