<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/billing/addons` — add-ons del plan. */
final class AddonsResource extends Resource
{
    /** @return mixed */
    public function list(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/billing/addons");
    }

    /** @param array<string,mixed> $input addonType, quantity, orgId? @return mixed */
    public function preview(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/billing/addons/preview", ['body' => $input]);
    }

    /** @param array<string,mixed> $input addonType, quantity, orgId? @return mixed */
    public function update(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/billing/addons", ['body' => $input]);
    }
}
