<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/messages` — envío y gestión de mensajes. */
final class MessagesResource extends Resource
{
    /**
     * Envía un mensaje (texto, media, plantilla, etc.).
     *
     * @param array<string,mixed> $input phoneNumberId, to, type, payload|templateId|variables, orgId?
     * @param array{idempotencyKey?:string} $options
     * @return mixed el mensaje creado
     */
    public function send(array $input, array $options = [])
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        $opts = ['body' => $input];
        if (isset($options['idempotencyKey'])) {
            $opts['idempotencyKey'] = $options['idempotencyKey'];
        }
        return $this->http->request('POST', "/organizations/{$orgId}/messages", $opts);
    }

    /**
     * Edita el texto de un mensaje (web-chat).
     *
     * @return mixed
     */
    public function edit(string $messageId, string $body, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('PATCH', "/organizations/{$orgId}/messages/{$messageId}/edit", [
            'body' => ['body' => $body],
        ]);
    }

    public function delete(string $messageId, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('DELETE', "/organizations/{$orgId}/messages/{$messageId}");
    }
}
