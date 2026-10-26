<?php

declare(strict_types=1);

namespace Mosend\Resources;

/**
 * `/organizations/{orgId}/signup-links` — enlaces de suscripción (In-App Signup
 * de Meta): deep links `wa.me/<tel>/signup/<id>` con opt-in verificado. No hay
 * borrado: se desactivan con `update($id, ['status' => 'DISABLED'])`.
 */
final class SignupLinksResource extends Resource
{
    /** @param array<string,mixed> $query wabaId?, orgId? @return mixed */
    public function list(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/signup-links", ['query' => $query]);
    }

    /**
     * @param array<string,mixed> $input wabaId, signupMessage, confirmationMessage (admite {{promo_code}}),
     *   privacyPolicyUrl (inmutable), websiteUrl?, promoCode?, displayName?, orgId?
     * @return mixed
     */
    public function create(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/signup-links", ['body' => $input]);
    }

    /** @return mixed */
    public function retrieve(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/signup-links/{$id}");
    }

    /**
     * @param array<string,mixed> $input status? ('ACTIVE'|'DISABLED'), signupMessage?, confirmationMessage?,
     *   promoCode?, displayName?, websiteUrl?, orgId?
     * @return mixed
     */
    public function update(string $id, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/signup-links/{$id}", ['body' => $input]);
    }
}
