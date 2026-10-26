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

    /** @return mixed */
    public function restore(string $messageId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/messages/{$messageId}/restore");
    }

    /** URL firmada del adjunto de un mensaje. @return mixed */
    public function getMedia(string $messageId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/messages/{$messageId}/media");
    }

    /**
     * Sube un adjunto y devuelve su `mediaId` para usarlo luego en `send()`.
     *
     * @param string|\CURLFile $file ruta del archivo o CURLFile
     * @return mixed
     */
    public function upload($file, string $phoneNumberId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/messages/upload", [
            'query' => ['phoneNumberId' => $phoneNumberId],
            'multipart' => ['file' => $file],
        ]);
    }

    /**
     * Mensaje de servicio interactivo: lista, botón CTA URL, pedir ubicación o datos de contacto.
     *
     * @param array<string,mixed> $input phoneNumberId, to, kind ('list'|'cta_url'|'location_request'|'request_contact_info'),
     *   bodyText?, headerText?, footerText?, buttonText?, sections?, displayText?, url?, replyToMessageId?, orgId?
     * @param array{idempotencyKey?:string} $options
     * @return mixed
     */
    public function sendInteractive(array $input, array $options = [])
    {
        return $this->postSend('/interactive', $input, $options);
    }

    /**
     * Mensaje de producto de WhatsApp Commerce (uno, varios o el catálogo).
     *
     * @param array<string,mixed> $input phoneNumberId, to, catalogId, bodyText?, footerText?, headerText?,
     *   productRetailerId?, products?, sections? [{title, productRetailerIds}], catalogMessage?,
     *   thumbnailProductRetailerId?, replyToMessageId?, orgId?
     * @param array{idempotencyKey?:string} $options
     * @return mixed
     */
    public function sendProduct(array $input, array $options = [])
    {
        return $this->postSend('/product', $input, $options);
    }

    /**
     * Mensaje de WhatsApp Flow (formulario nativo).
     *
     * @param array<string,mixed> $input phoneNumberId, to, flowId, bodyText, ctaText, headerText?, footerText?,
     *   screen?, data?, flowAction? ('navigate'|'data_exchange'), replyToMessageId?, orgId?
     * @param array{idempotencyKey?:string} $options
     * @return mixed
     */
    public function sendFlow(array $input, array $options = [])
    {
        return $this->postSend('/flow', $input, $options);
    }

    /**
     * Carrusel multimedia interactivo (hasta 10 tarjetas).
     *
     * @param array<string,mixed> $input phoneNumberId, to, bodyText, cards [{headerType ('image'|'video'), headerLink,
     *   bodyText?, ctaUrl? {displayText, url}, quickReplies? [{id, title}]}], replyToMessageId?, orgId?
     * @param array{idempotencyKey?:string} $options
     * @return mixed
     */
    public function sendCarousel(array $input, array $options = [])
    {
        return $this->postSend('/carousel', $input, $options);
    }

    /** Muestra el contenido de un mensaje que el cliente eliminó. @return mixed */
    public function reveal(string $messageId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/messages/{$messageId}/revelar");
    }

    /**
     * @param array<string,mixed> $input
     * @param array{idempotencyKey?:string} $options
     * @return mixed
     */
    private function postSend(string $ruta, array $input, array $options)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        $opts = ['body' => $input];
        if (isset($options['idempotencyKey'])) {
            $opts['idempotencyKey'] = $options['idempotencyKey'];
        }
        return $this->http->request('POST', "/organizations/{$orgId}/messages{$ruta}", $opts);
    }
}
