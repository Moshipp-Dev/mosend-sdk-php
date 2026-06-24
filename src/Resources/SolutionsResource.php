<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/solutions` (catálogo) + soluciones instaladas por organización. */
final class SolutionsResource extends Resource
{
    /** Catálogo de soluciones verticales disponibles. @return mixed */
    public function list()
    {
        return $this->http->request('GET', '/solutions');
    }

    /** Detalle completo de un pack por slug. @return mixed */
    public function retrieve(string $slug)
    {
        return $this->http->request('GET', "/solutions/{$slug}");
    }

    /** Soluciones instaladas en la organización. @return mixed */
    public function listInstalls(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/solutions");
    }

    /** @param array<string,mixed> $input wabaId?, orgId? @return mixed */
    public function install(string $slug, array $input = [])
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/solutions/{$slug}/install", ['body' => $input]);
    }

    /** Desinstala el pack en la organización. @return mixed */
    public function uninstall(string $slug, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('DELETE', "/organizations/{$orgId}/solutions/{$slug}");
    }
}
