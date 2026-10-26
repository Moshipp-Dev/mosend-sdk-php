<?php

declare(strict_types=1);

namespace Mosend\Resources;

/**
 * `/webhooks/wompi` — webhook de eventos de Wompi (cobro a cliente final).
 * Público: Wompi lo llama de servidor a servidor y la firma va dentro del
 * evento. Sirve para reenviar a Mosend un evento recibido en otro lado (p. ej.
 * el plugin de WooCommerce). Responde 200 aunque el evento se descarte.
 */
final class WompiResource extends Resource
{
    /** @param array<string,mixed> $event event, data, signature?{checksum, properties}, timestamp?, environment? @return mixed */
    public function webhook(array $event)
    {
        return $this->http->request('POST', '/webhooks/wompi', ['body' => $event, 'skipAuth' => true]);
    }

    /** URL propia de cada organización (`/webhooks/wompi/{orgId}`). @param array<string,mixed> $event @return mixed */
    public function webhookForOrg(array $event, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/webhooks/wompi/{$orgId}", ['body' => $event, 'skipAuth' => true]);
    }
}
