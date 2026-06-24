<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/store-templates` — catálogo y creación de plantillas de e-commerce. */
final class StoreTemplatesResource extends Resource
{
    /** Catálogo de plantillas de e-commerce listas para instalar. @return mixed */
    public function catalog(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/store-templates/catalog");
    }

    /** Variables disponibles para el editor de plantillas. @return mixed */
    public function variables(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/store-templates/variables");
    }

    /** Estado de instalación/aprobación de cada plantilla. @param array<string,mixed> $query wabaId?, connectionId?, orgId? @return mixed */
    public function status(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/store-templates/status", ['query' => $query]);
    }

    /**
     * Crea una plantilla del catálogo en Meta (y opcionalmente el mapeo).
     *
     * @param array<string,mixed> $input catalogKey, wabaId?, language?, name?,
     *   connectionId?, bodyText?, bodyExample?, headerText?, headerExample?,
     *   footer?, variableMap?, orgId?
     * @return mixed
     */
    public function install(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/store-templates/install", ['body' => $input]);
    }
}
