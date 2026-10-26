<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/bot/images` — librería de imágenes que el bot puede enviar. */
final class BotImagesResource extends Resource
{
    /**
     * Imágenes que el bot puede enviar.
     *
     * @return mixed
     */
    public function list(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/bot/images");
    }

    /**
     * Sube una imagen a la librería del bot (multipart).
     *
     * @param array<string,mixed> $input file (ruta|CURLFile), label?, caption?, orgId?
     * @return mixed
     */
    public function upload(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        $multipart = ['file' => $input['file']];
        if (isset($input['label'])) {
            $multipart['label'] = (string) $input['label'];
        }
        if (isset($input['caption'])) {
            $multipart['caption'] = (string) $input['caption'];
        }
        return $this->http->request('POST', "/organizations/{$orgId}/bot/images", ['multipart' => $multipart]);
    }

    /**
     * Renombra la imagen o cambia su pie de foto.
     *
     * @param array<string,mixed> $input label?, caption?, orgId?
     * @return mixed
     */
    public function update(string $id, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/bot/images/{$id}", ['body' => $input]);
    }

    /**
     * Quita la imagen de la librería. El archivo se conserva porque los mensajes
     * ya enviados lo referencian. Con $forzar la quita aunque un agente la use.
     *
     * @return mixed
     */
    public function delete(string $id, bool $forzar = false, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        $query = $forzar ? ['forzar' => 'true'] : [];
        return $this->http->request('DELETE', "/organizations/{$orgId}/bot/images/{$id}", ['query' => $query]);
    }
}
