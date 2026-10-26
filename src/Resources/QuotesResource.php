<?php

declare(strict_types=1);

namespace Mosend\Resources;

/**
 * `/organizations/{orgId}/cotizaciones` — cotizaciones en PDF (función de plan
 * `quotes`): borradores, envío por la conversación, estado y conversión en
 * pedido de Ventas.
 */
final class QuotesResource extends Resource
{
    /** Si el plan incluye cotizaciones y si el PDF sale sin marca de agua. @return mixed */
    public function availability(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/cotizaciones/disponibilidad");
    }

    /** Empresas con las que puede salir una cotización. @param array<string,mixed> $query conversationId?, contactId?, orgId? @return mixed */
    public function companies(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/cotizaciones/empresas", ['query' => $query]);
    }

    /**
     * Sube el logo de una empresa (PNG o JPG, hasta 2 MB; campo `file`).
     *
     * @param string|\CURLFile $file
     * @return mixed
     */
    public function uploadLogo($file, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/cotizaciones/emisor/logo", [
            'multipart' => ['file' => $file],
        ]);
    }

    /** Lo que se puede cotizar. @param array<string,mixed> $query conversationId?, contactId?, orgId? @return mixed */
    public function catalog(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/cotizaciones/catalogo", ['query' => $query]);
    }

    /**
     * @param array<string,mixed> $query contactId?, conversationId?,
     *   estado?(BORRADOR|ENVIADA|ACEPTADA|RECHAZADA|VENCIDA|CONVERTIDA), empresa?, orgId?
     * @return mixed
     */
    public function list(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/cotizaciones", ['query' => $query]);
    }

    /**
     * Crea un borrador.
     *
     * @param array<string,mixed> $input lineas: [{origen, descripcion, cantidad, precioUnitarioCents, origenId?,
     *   varianteId?, detalle?, descuentoPorcentaje?, ivaPorcentaje?}], contactId?, conversationId?,
     *   billingProfileId?, perfilDeCobroId?, empresaId?, moneda?, vigenciaDias?, cliente*?, formasDePago?,
     *   condiciones?, notas?, orgId?
     * @return mixed
     */
    public function create(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/cotizaciones", ['body' => $input]);
    }

    /** @return mixed */
    public function get(string $quoteId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/cotizaciones/{$quoteId}");
    }

    /** Edita un borrador (una enviada se duplica). @param array<string,mixed> $input mismos campos que create, orgId? @return mixed */
    public function update(string $quoteId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PUT', "/organizations/{$orgId}/cotizaciones/{$quoteId}", ['body' => $input]);
    }

    /** Borra un borrador. @return mixed */
    public function delete(string $quoteId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('DELETE', "/organizations/{$orgId}/cotizaciones/{$quoteId}");
    }

    /** Bytes del PDF de la cotización. */
    public function pdf(string $quoteId, ?string $orgId = null): string
    {
        $orgId = $this->requireOrgId($orgId);
        return (string) $this->http->request('GET', "/organizations/{$orgId}/cotizaciones/{$quoteId}/pdf", ['raw' => true]);
    }

    /** Enlace firmado (10 minutos) para ver o descargar el PDF. @return mixed */
    public function viewLink(string $quoteId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/cotizaciones/{$quoteId}/enlace");
    }

    /** Envía la cotización por la conversación del cliente. @param array<string,mixed> $input mensaje?, orgId? @return mixed */
    public function send(string $quoteId, array $input = [])
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/cotizaciones/{$quoteId}/enviar", ['body' => $input]);
    }

    /** @return mixed */
    public function duplicate(string $quoteId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/cotizaciones/{$quoteId}/duplicar");
    }

    /** @param array<string,mixed> $input estado(ACEPTADA|RECHAZADA), orgId? @return mixed */
    public function setStatus(string $quoteId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/cotizaciones/{$quoteId}/estado", ['body' => $input]);
    }

    /** Convierte la cotización en un pedido de Ventas. @return mixed */
    public function convert(string $quoteId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/cotizaciones/{$quoteId}/convertir");
    }

    /** `GET /cotizaciones/pdf/{token}` (público): bytes del PDF de una cotización enviada. */
    public function publicPdf(string $token): string
    {
        return (string) $this->http->request('GET', '/cotizaciones/pdf/' . rawurlencode($token), [
            'raw' => true,
            'skipAuth' => true,
        ]);
    }

    /**
     * `GET /cotizaciones/vista/{token}` (público): bytes del PDF por el enlace
     * de vista de `viewLink()`.
     *
     * @param array<string,mixed> $query descargar? (cualquier valor = attachment)
     */
    public function viewPdf(string $token, array $query = []): string
    {
        return (string) $this->http->request('GET', '/cotizaciones/vista/' . rawurlencode($token), [
            'query' => $query,
            'raw' => true,
            'skipAuth' => true,
        ]);
    }
}
