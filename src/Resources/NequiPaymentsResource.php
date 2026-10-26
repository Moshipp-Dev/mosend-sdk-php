<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/billing/nequi` — pagar facturas o recargar saldo con Nequi (push o QR). */
final class NequiPaymentsResource extends Resource
{
    /** @return mixed */
    public function enabled(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/billing/nequi/enabled");
    }

    /** @param array<string,mixed> $input phoneNumber (10 dígitos), invoiceId?, amount?, orgId? @return mixed */
    public function createPush(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/billing/nequi/push", ['body' => $input]);
    }

    /** @param array<string,mixed> $input invoiceId?, amount?, orgId? @return mixed */
    public function createQr(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/billing/nequi/qr", ['body' => $input]);
    }

    /** Estado del pago (consulta en vivo a Nequi si sigue pendiente). @return mixed */
    public function status(string $paymentId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/billing/nequi/{$paymentId}/status");
    }

    /** @return mixed */
    public function cancel(string $paymentId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/billing/nequi/{$paymentId}/cancel");
    }
}
