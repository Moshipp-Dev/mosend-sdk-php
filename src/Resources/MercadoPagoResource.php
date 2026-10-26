<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** Pagos Mercado Pago: recarga de wallet, pago de facturas y webhook IPN. */
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

    /**
     * `POST /webhooks/mercado-pago` (público): webhook IPN de Mercado Pago. Lo
     * llama MP de servidor a servidor; sirve para reenviar a Mosend una
     * notificación recibida en otro lado. La firma va en las cabeceras
     * `x-signature` / `x-request-id` (pásalas en `$options['headers']`).
     *
     * @param array<string,mixed> $body type?, action?, data?{id}
     * @param array<string,mixed> $options query? (type?, id?), headers? (x-signature, x-request-id)
     * @return mixed
     */
    public function webhook(array $body, array $options = [])
    {
        $opts = ['body' => $body, 'skipAuth' => true];
        if (!empty($options['query'])) {
            $opts['query'] = $options['query'];
        }
        if (!empty($options['headers'])) {
            $opts['headers'] = $options['headers'];
        }
        return $this->http->request('POST', '/webhooks/mercado-pago', $opts);
    }
}
