<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/billing/alert-settings` — alertas de saldo. */
final class WalletAlertsResource extends Resource
{
    /** @return mixed */
    public function retrieve(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/billing/alert-settings");
    }

    /** @param array<string,mixed> $input lowBalanceThreshold?, autoRechargeEnabled?, ..., orgId? @return mixed */
    public function update(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/billing/alert-settings", ['body' => $input]);
    }
}
