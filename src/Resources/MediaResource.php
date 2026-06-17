<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/media` — lectura de media. */
final class MediaResource extends Resource
{
    /** @return mixed */
    public function retrieve(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/media/{$id}");
    }

    /** @return mixed */
    public function getUrl(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/media/{$id}/url");
    }
}
