<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/messaging-limit` — límite diario de mensajes de Meta por portafolio. */
final class MessagingLimitResource extends Resource
{
    /**
     * Nivel, contactados con plantilla en 24 h, disponibles y avisos de Meta de 30 días. Con phoneNumberId, además el portafolio de ese número.
     *
     * @param array<string,mixed> $query phoneNumberId?, orgId?
     * @return mixed
     */
    public function retrieve(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/messaging-limit", ['query' => $query]);
    }
}
