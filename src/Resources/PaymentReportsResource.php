<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/billing/payment-reports` — pagos por transferencia Bre-B/QR con comprobante. */
final class PaymentReportsResource extends Resource
{
    /** Llave Bre-B / QR de cobro de Mosend. @return mixed */
    public function brebInfo(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/billing/payment-reports/breb-info");
    }

    /**
     * Reporta un pago: comprobante (campo `receipt`) + monto, contra una
     * factura o como recarga de saldo.
     *
     * @param string|\CURLFile $receipt
     * @param array<string,mixed> $input amount, invoiceId?, note?, orgId?
     * @return mixed
     */
    public function create($receipt, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        $multipart = ['receipt' => $receipt];
        foreach ($input as $k => $v) {
            if ($v !== null) {
                $multipart[$k] = (string) $v;
            }
        }
        return $this->http->request('POST', "/organizations/{$orgId}/billing/payment-reports", [
            'multipart' => $multipart,
        ]);
    }

    /** @return mixed */
    public function list(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/billing/payment-reports");
    }

    /** URL firmada del comprobante. @return mixed */
    public function receiptUrl(string $reportId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/billing/payment-reports/{$reportId}/receipt");
    }
}
