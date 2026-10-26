<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/deletion-request` — eliminación de la organización con 30 días de gracia. */
final class AccountDeletionResource extends Resource
{
    /**
     * Estado de la solicitud de eliminación (cualquier miembro).
     *
     * @return mixed
     */
    public function status(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/deletion-request");
    }

    /**
     * Solicita la eliminación de la organización (solo owner).
     *
     * @param array<string,mixed> $input confirmOrgName, reason?, orgId?
     * @return mixed
     */
    public function request(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/deletion-request", ['body' => $input]);
    }

    /**
     * Cancela la eliminación durante el periodo de gracia (solo owner).
     *
     * @return mixed
     */
    public function cancel(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('DELETE', "/organizations/{$orgId}/deletion-request");
    }
}
