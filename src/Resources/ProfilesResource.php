<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/phone-numbers/{phoneId}/profile` — perfil de negocio. */
final class ProfilesResource extends Resource
{
    /** @return mixed */
    public function retrieve(string $phoneId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/phone-numbers/{$phoneId}/profile");
    }

    /** @param array<string,mixed> $input address?, description?, email?, vertical?, websites?, orgId? @return mixed */
    public function update(string $phoneId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PUT', "/organizations/{$orgId}/phone-numbers/{$phoneId}/profile", ['body' => $input]);
    }

    /**
     * @param string|\CURLFile $file ruta o CURLFile de la imagen de perfil
     * @return mixed
     */
    public function uploadPicture(string $phoneId, $file, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/phone-numbers/{$phoneId}/profile/picture", [
            'multipart' => ['file' => $file],
        ]);
    }
}
