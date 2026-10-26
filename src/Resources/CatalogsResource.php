<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/catalog` — catálogo de Meta Commerce conectado a la WABA. */
final class CatalogsResource extends Resource
{
    /** Detecta el catálogo de la WABA, lo guarda y sincroniza productos. @return mixed */
    public function connect(string $wabaId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/catalog/waba/{$wabaId}/connect");
    }

    /** @return mixed */
    public function sync(string $wabaId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/catalog/waba/{$wabaId}/sync");
    }

    /** Productos cacheados. @param array<string,mixed> $query catalogId?, orgId? @return mixed */
    public function listProducts(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/catalog/products", ['query' => $query]);
    }

    /** @param array<string,mixed> $query phoneNumberId, orgId? @return mixed */
    public function getCommerceSettings(array $query)
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/catalog/commerce-settings", ['query' => $query]);
    }

    /**
     * @param array<string,mixed> $input phoneNumberId (va en query), isCartEnabled?, isCatalogVisible?, orgId?
     * @return mixed
     */
    public function updateCommerceSettings(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        $query = ['phoneNumberId' => $input['phoneNumberId'] ?? null];
        unset($input['orgId'], $input['phoneNumberId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/catalog/commerce-settings", [
            'query' => $query,
            'body' => $input,
        ]);
    }
}
