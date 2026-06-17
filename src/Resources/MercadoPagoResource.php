<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** Pagos Mercado Pago: recarga de wallet y pago de facturas. */
final class MercadoPagoResource extends Resource
{
    /** @param array<string,mixed> $input amount, currency, returnTo?, orgId? @return mixed */
    public function rechargeWallet(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/wallet/recharge", ['body' => $input]);
    }

    /** @param array<string,mixed> $input orgId? @return mixed */
    public function payInvoice(string $invoiceId, array $input = [])
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/billing/invoices/{$invoiceId}/pay", ['body' => $input]);
    }
}
