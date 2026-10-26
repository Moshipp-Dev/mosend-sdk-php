<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/orders` — pedidos recibidos por el carrito de WhatsApp. */
final class OrdersResource extends Resource
{
    /** Pedido asociado a un mensaje del inbox. @return mixed */
    public function getByMessage(string $messageId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/orders/by-message/{$messageId}");
    }

    /** @param array<string,mixed> $input status(RECEIVED|PROCESSING|COMPLETED|CANCELLED), orgId? @return mixed */
    public function updateStatus(string $orderId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/orders/{$orderId}", ['body' => $input]);
    }
}
