<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/ai-credits` — créditos de IA. */
final class AiCreditsResource extends Resource
{
    /** @return mixed */
    public function summary(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/ai-credits/summary");
    }

    /** @param array<string,mixed> $query limit?, orgId? @return mixed */
    public function transactions(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/ai-credits/transactions", ['query' => $query]);
    }

    /**
     * Política pública del crédito de IA (bienvenida y packs). Sin autenticación.
     * @return mixed
     */
    public function publicPolicy()
    {
        return $this->http->request('GET', '/ai-credits/public-policy');
    }

    /**
     * Policy global del AI Power Pack. Solo staff de Mosend.
     * @return mixed
     */
    public function getPolicy()
    {
        return $this->http->request('GET', '/admin/ai-credit-policy');
    }

    /**
     * Actualiza la policy global. Solo staff de Mosend.
     *
     * @param array<string,mixed> $input enabled?, signupCreditUsd?, lowBalanceThresholdUsd?, softDailyLimitUsd?, rechargePacks? [{amountUsd, bonusUsd}], minCustomRechargeUsd?
     * @return mixed
     */
    public function updatePolicy(array $input)
    {
        return $this->http->request('PATCH', '/admin/ai-credit-policy', ['body' => $input]);
    }

    /**
     * Ajuste manual del balance de una org (bonus, refund, corrección). Solo staff de Mosend.
     *
     * @param array<string,mixed> $input amountUsd, note
     * @return mixed
     */
    public function adjust(string $organizationId, array $input)
    {
        return $this->http->request('POST', "/admin/organizations/{$organizationId}/ai-credits/adjust", ['body' => $input]);
    }
}
