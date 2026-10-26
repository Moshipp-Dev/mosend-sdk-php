<?php

declare(strict_types=1);

namespace Mosend\Resources;

/**
 * Mosend como proveedor OAuth 2.1, endpoint por endpoint.
 *
 * Los endpoints del estándar (`/oauth/token`, `/oauth/revoke`, `/oauth/introspect`)
 * usan los nombres de las RFC (`grant_type`, `refresh_token`, `client_id`…) y
 * responden sin el sobre `{ data }`.
 */
final class OAuthResource extends Resource
{
    /**
     * Inicio del flujo de autorización. Responde con una redirección 302 a la pantalla «Autorizar acceso», pensada para el navegador de la persona: normalmente se arma la URL y se redirige allí. Llamarlo desde el servidor sirve para validar los parámetros (un error vuelve como 4xx).
     *
     * @param array<string,mixed> $query response_type, client_id, redirect_uri, scope, state, code_challenge, code_challenge_method (S256), prompt?
     * @return mixed
     */
    public function authorize(array $query)
    {
        return $this->http->request('GET', '/oauth/authorize', ['query' => $query, 'skipAuth' => true]);
    }

    /**
     * Canje de código (con PKCE) o renovación (RFC 6749).
     *
     * @param array<string,mixed> $input grant_type (authorization_code|refresh_token), code?, redirect_uri?, code_verifier?, refresh_token?, scope?, client_id?, client_secret?
     * @return mixed
     */
    public function token(array $input)
    {
        return $this->http->request('POST', '/oauth/token', ['body' => $input, 'skipAuth' => true]);
    }

    /**
     * Revoca un token (RFC 7009). Siempre 200.
     *
     * @param array<string,mixed> $input token, token_type_hint?, client_id?, client_secret?
     * @return mixed
     */
    public function revoke(array $input)
    {
        return $this->http->request('POST', '/oauth/revoke', ['body' => $input, 'skipAuth' => true]);
    }

    /**
     * Estado de un token (RFC 7662). Solo la aplicación dueña del token.
     *
     * @param array<string,mixed> $input token, token_type_hint?, client_id?, client_secret?
     * @return mixed
     */
    public function introspect(array $input)
    {
        return $this->http->request('POST', '/oauth/introspect', ['body' => $input, 'skipAuth' => true]);
    }

    /**
     * Metadatos del servidor de autorización (RFC 8414), en la ruta de descubrimiento estándar.
     * @return mixed
     */
    public function metadata()
    {
        return $this->http->request('GET', '/.well-known/oauth-authorization-server', ['skipAuth' => true]);
    }

    /**
     * El mismo documento de metadata() bajo /oauth/metadata, para clientes que no consultan /.well-known.
     * @return mixed
     */
    public function metadataAlias()
    {
        return $this->http->request('GET', '/oauth/metadata', ['skipAuth' => true]);
    }

    /**
     * Identidad y organización de la concesión. Requiere un token OAuth con organizations:read.
     * @return mixed
     */
    public function userinfo()
    {
        return $this->http->request('GET', '/oauth/userinfo');
    }

    /**
     * Detalle de una solicitud de autorización pendiente. Lo usa la pantalla «Autorizar acceso» con la sesión JWT del panel, no una API key.
     * @return mixed
     */
    public function getRequest(string $id)
    {
        return $this->http->request('GET', "/oauth/requests/{$id}");
    }

    /**
     * Permite o rechaza la solicitud (sesión del panel; las API keys no pueden). Devuelve redirectTo con code o error.
     *
     * @param array<string,mixed> $input approve, organizationId?
     * @return mixed
     */
    public function decide(string $id, array $input)
    {
        return $this->http->request('POST', "/oauth/requests/{$id}/decision", ['body' => $input]);
    }

    /**
     * Aplicaciones con acceso a la organización.
     *
     * @return mixed
     */
    public function listGrants(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/oauth/grants");
    }

    /**
     * Quita el acceso a una aplicación: revoca la concesión y todos sus tokens.
     */
    public function revokeGrant(string $id, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('DELETE', "/organizations/{$orgId}/oauth/grants/{$id}");
    }
}
