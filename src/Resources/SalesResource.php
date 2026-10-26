<?php

declare(strict_types=1);

namespace Mosend\Resources;

/**
 * `/organizations/{orgId}/sales` — Ventas: catálogo de productos, pedidos,
 * perfiles de facturación y facturación electrónica DIAN (empresas emisoras,
 * facturas, notas crédito y facturas de ventas de licencias).
 */
final class SalesResource extends Resource
{
    // ─── Entitlement y add-ons ───────────────────────────────────────────────

    /** Si Ventas y la Facturación electrónica están incluidas (plan vs add-on). @return mixed */
    public function entitlement(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/sales/entitlement");
    }

    /** @param array<string,mixed> $input salesBilling?, einvoicing?, orgId? @return mixed */
    public function setAddons(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PUT', "/organizations/{$orgId}/sales/addons", ['body' => $input]);
    }

    // ─── Productos ───────────────────────────────────────────────────────────

    /** @param array<string,mixed> $query q?, active?('true'|'false'), orgId? @return mixed */
    public function listProducts(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/sales/products", ['query' => $query]);
    }

    /** @return mixed */
    public function getProduct(string $productId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/sales/products/{$productId}");
    }

    /**
     * @param array<string,mixed> $input name, priceCents, sku?, description?, currency?(COP|USD|MXN), taxRate?,
     *   unitCode?, imageMediaId?, active?, orgId?
     * @return mixed
     */
    public function createProduct(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/sales/products", ['body' => $input]);
    }

    /** @param array<string,mixed> $input mismos campos que createProduct (opcionales), orgId? @return mixed */
    public function updateProduct(string $productId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/sales/products/{$productId}", ['body' => $input]);
    }

    /** @return mixed */
    public function deleteProduct(string $productId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('DELETE', "/organizations/{$orgId}/sales/products/{$productId}");
    }

    // ─── Pedidos ─────────────────────────────────────────────────────────────

    /**
     * @param array<string,mixed> $input items: [{productId?, description?, quantity, unitPriceCents?, taxRate?}],
     *   contactId?, conversationId?, billingProfileId?, channel?(WHATSAPP|WEB|INSTAGRAM|STORE|MANUAL),
     *   currency?, notes?, orgId?
     * @return mixed
     */
    public function createOrder(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/sales/orders", ['body' => $input]);
    }

    /** @param array<string,mixed> $query status?, contactId?, conversationId?, orgId? @return mixed */
    public function listOrders(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/sales/orders", ['query' => $query]);
    }

    /** @return mixed */
    public function getOrder(string $orderId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/sales/orders/{$orderId}");
    }

    /** @param array<string,mixed> $input status(DRAFT|CONFIRMED|PAID|FULFILLED|CANCELLED), orgId? @return mixed */
    public function updateOrderStatus(string $orderId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/sales/orders/{$orderId}/status", ['body' => $input]);
    }

    /** Solo pedidos en borrador o cancelados. @return mixed */
    public function deleteOrder(string $orderId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('DELETE', "/organizations/{$orgId}/sales/orders/{$orderId}");
    }

    // ─── Perfiles de facturación ─────────────────────────────────────────────

    /** @param array<string,mixed> $query contactId?, orgId? @return mixed */
    public function listBillingProfiles(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/sales/billing-profiles", ['query' => $query]);
    }

    /** @return mixed */
    public function getBillingProfile(string $profileId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/sales/billing-profiles/{$profileId}");
    }

    /**
     * @param array<string,mixed> $input legalName, docNumber, contactId?, dv?, email?, phone?, address?,
     *   municipalityId?, typeDocumentId?, typeOrganizationId?, typeRegimeId?, typeLiabilityId?, isDefault?, orgId?
     * @return mixed
     */
    public function createBillingProfile(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/sales/billing-profiles", ['body' => $input]);
    }

    /** @param array<string,mixed> $input mismos campos que createBillingProfile (opcionales, sin contactId), orgId? @return mixed */
    public function updateBillingProfile(string $profileId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/sales/billing-profiles/{$profileId}", ['body' => $input]);
    }

    /** @return mixed */
    public function deleteBillingProfile(string $profileId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('DELETE', "/organizations/{$orgId}/sales/billing-profiles/{$profileId}");
    }

    // ─── Facturación electrónica: empresas emisoras ──────────────────────────

    /** Config del conector (sin el token). @param array<string,mixed> $query configId?, orgId? @return mixed */
    public function getEinvoicingConfig(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/sales/einvoicing/config", ['query' => $query]);
    }

    /** @param array<string,mixed> $input configId?, nueva?, name?, isDefault?, baseUrl?, apiKey?, … orgId? @return mixed */
    public function updateEinvoicingConfig(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PUT', "/organizations/{$orgId}/sales/einvoicing/config", ['body' => $input]);
    }

    /** Valida el token y trae la numeración vigente. @param array<string,mixed> $input configId?, apiKey?, idSoftware?, baseUrl?, orgId? @return mixed */
    public function validateEinvoicing(array $input = [])
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/sales/einvoicing/validate", ['body' => $input]);
    }

    /** Empresas emisoras (la predeterminada primero). @return mixed */
    public function listEinvoicingCompanies(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/sales/einvoicing/companies");
    }

    /** @param array<string,mixed> $query conversationId?, customerOrderId?, orderId?, orgId? @return mixed */
    public function suggestEinvoicingCompany(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/sales/einvoicing/companies/sugerida", ['query' => $query]);
    }

    /** Solo empresas sin documentos. @return mixed */
    public function deleteEinvoicingCompany(string $configId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('DELETE', "/organizations/{$orgId}/sales/einvoicing/companies/{$configId}");
    }

    /** Importa los clientes del proveedor como perfiles de facturación. @param array<string,mixed> $query configId?, orgId? @return mixed */
    public function importEinvoicingCustomers(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/sales/einvoicing/customers/import", ['query' => $query]);
    }

    /** @param array<string,mixed> $query doc, docType?, configId?, orgId? @return mixed */
    public function lookupEinvoicingCustomer(array $query)
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/sales/einvoicing/customer-lookup", ['query' => $query]);
    }

    // ─── Facturación electrónica: documentos ─────────────────────────────────

    /** @param array<string,mixed> $query orderId?, contactId?, status?, docType?(INVOICE|CREDIT_NOTE), configId?, orgId? @return mixed */
    public function listInvoices(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/sales/einvoicing/invoices", ['query' => $query]);
    }

    /**
     * Factura un pedido (`orderId`) o líneas directas (`items`, excluyente).
     *
     * @param array<string,mixed> $input orderId?, items?, contactId?, conversationId?, notes?, idempotencyKey?,
     *   configId?, billingProfileId?, paymentFormId?, paymentMethodId?, orgId?
     * @return mixed
     */
    public function generateInvoice(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/sales/einvoicing/invoices", ['body' => $input]);
    }

    /** Nota crédito total que anula una factura. @param array<string,mixed> $input invoiceId, discrepancyCode?, reason?, force?, orgId? @return mixed */
    public function createCreditNote(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/sales/einvoicing/credit-notes", ['body' => $input]);
    }

    /**
     * @param array<string,mixed> $input customerOrderId, configId?, billingProfileId?, legalName?, docNumber?, dv?,
     *   email?, address?, typeDocumentId?, typeOrganizationId?, typeRegimeId?, typeLiabilityId?, paymentFormId?,
     *   paymentMethodId?, items?, notes?, orgId?
     * @return mixed
     */
    public function invoiceLicenseOrder(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/sales/einvoicing/license-orders/invoice", ['body' => $input]);
    }

    /** @param array<string,mixed> $input customerOrderId, orgId? @return mixed */
    public function createLicenseInvoiceDraft(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/sales/einvoicing/license-orders/draft", ['body' => $input]);
    }

    /** Envía la factura/nota al cliente por WhatsApp, en su conversación. @return mixed */
    public function sendInvoice(string $invoiceId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/sales/einvoicing/invoices/{$invoiceId}/send");
    }

    /** PDF en base64: `{ filename, contentType, base64 }`. @return mixed */
    public function getInvoicePdf(string $invoiceId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/sales/einvoicing/invoices/{$invoiceId}/pdf");
    }

    /** `GET /sales/invoice-pdf/{token}` (público): bytes del PDF. */
    public function publicInvoicePdf(string $token): string
    {
        return (string) $this->http->request('GET', '/sales/invoice-pdf/' . rawurlencode($token), [
            'raw' => true,
            'skipAuth' => true,
        ]);
    }
}
