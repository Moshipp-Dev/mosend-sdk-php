<?php

declare(strict_types=1);

namespace Mosend\Resources;

/**
 * `/organizations/{orgId}/license-delivery` — licencias digitales: productos y
 * variantes, inventario de llaves, pedidos y entregas, cupones, listas de precios,
 * cobros (agrupados y libres), bandeja de pagos, comprobantes, marcas que cobran,
 * cuentas y llaves de transferencia. Los campos van 1:1 con la API.
 */
final class LicenseDeliveryResource extends Resource
{
    // --- Ajustes ---

    /** Ajustes de licencias digitales de la organización. @return mixed */
    public function getSettings(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/license-delivery/settings");
    }

    /**
     * Actualiza los ajustes (pasarela, transferencia, bandeja, renovación, etc.).
     *
     * @param array<string,mixed> $input enabled?, wompiEnv?, wompiPublicKey?, wompiPrivateKey?, wompiEventsSecret?, wompiIntegritySecret?, deliveryTemplateName?, deliveryTemplateLang?, ... (ver `UpdateLicenseDeliverySettingsDto` en la API), orgId?
     * @return mixed
     */
    public function updateSettings(array $input = [])
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PUT', "/organizations/{$orgId}/license-delivery/settings", ['body' => $input]);
    }

    /**
     * Activa o desactiva el add-on de pago.
     *
     * @param array<string,mixed> $input enabled, orgId?
     * @return mixed
     */
    public function setPaidAddon(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PUT', "/organizations/{$orgId}/license-delivery/settings/addon", ['body' => $input]);
    }

    /**
     * Qué le falta a la organización (o a un número) para poder vender.
     *
     * @param array<string,mixed> $query phoneNumberId?, orgId?
     * @return mixed
     */
    public function sellingReadiness(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/license-delivery/selling-readiness", ['query' => $query]);
    }

    /**
     * Métricas de ventas de licencias en un rango.
     *
     * @param array<string,mixed> $query days?, desde?, hasta?, orgId?
     * @return mixed
     */
    public function analytics(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/license-delivery/analytics", ['query' => $query]);
    }

    // --- Cupones ---

    /** Lista los cupones de descuento. @return mixed */
    public function listCoupons(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/license-delivery/coupons");
    }

    /**
     * Crea un cupón.
     *
     * @param array<string,mixed> $input code, discountType?, discountValue, productId?, active?, maxRedemptions?, expiresAt?, orgId?
     * @return mixed
     */
    public function createCoupon(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/coupons", ['body' => $input]);
    }

    /**
     * Edita un cupón.
     *
     * @param array<string,mixed> $input code?, discountType?, discountValue?, productId?, active?, maxRedemptions?, expiresAt?, orgId?
     * @return mixed
     */
    public function updateCoupon(string $couponId, array $input = [])
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/license-delivery/coupons/{$couponId}", ['body' => $input]);
    }

    /** Elimina un cupón. @return mixed */
    public function deleteCoupon(string $couponId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('DELETE', "/organizations/{$orgId}/license-delivery/coupons/{$couponId}");
    }

    // --- Listas de precios ---

    /** Lista las listas de precios. @return mixed */
    public function listPriceLists(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/license-delivery/price-lists");
    }

    /**
     * Crea una lista de precios.
     *
     * @param array<string,mixed> $input name, tagNames?, discountPercent?, priority?, active?, orgId?
     * @return mixed
     */
    public function createPriceList(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/price-lists", ['body' => $input]);
    }

    /**
     * Edita una lista de precios.
     *
     * @param array<string,mixed> $input name?, tagNames?, discountPercent?, priority?, active?, orgId?
     * @return mixed
     */
    public function updatePriceList(string $priceListId, array $input = [])
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/license-delivery/price-lists/{$priceListId}", ['body' => $input]);
    }

    /** Elimina una lista de precios. @return mixed */
    public function deletePriceList(string $priceListId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('DELETE', "/organizations/{$orgId}/license-delivery/price-lists/{$priceListId}");
    }

    /**
     * Fija (o quita con priceCents null) el precio de un producto/variante en la lista.
     *
     * @param array<string,mixed> $input productId, variantId?, priceCents?, orgId?
     * @return mixed
     */
    public function setPriceListItem(string $priceListId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PUT', "/organizations/{$orgId}/license-delivery/price-lists/{$priceListId}/items", ['body' => $input]);
    }

    /**
     * Fija precios de la lista para varios productos a la vez.
     *
     * @param array<string,mixed> $input productIds, modo, valor?, redondeo?, incluirVariantes?, orgId?
     * @return mixed
     */
    public function bulkSetPriceListItems(string $priceListId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PUT', "/organizations/{$orgId}/license-delivery/price-lists/{$priceListId}/items/bulk", ['body' => $input]);
    }

    // --- Productos ---

    /**
     * Lista los productos (con precios resueltos si se pasa conversación/contacto).
     *
     * @param array<string,mixed> $query conversationId?, contactId?, orgId?
     * @return mixed
     */
    public function listProducts(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/license-delivery/products", ['query' => $query]);
    }

    /**
     * Crea un producto digital.
     *
     * @param array<string,mixed> $input name, description?, sku?, category?, imageUrl?, priceCents, durationDays?, renewalNoticeEnabled?, ... (ver `CreateDigitalProductDto` en la API), orgId?
     * @return mixed
     */
    public function createProduct(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/products", ['body' => $input]);
    }

    /**
     * Importa productos en lote (Excel/CSV).
     *
     * @param array<string,mixed> $input products, actualizarExistentes?, orgId?
     * @return mixed
     */
    public function importProducts(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/products/import", ['body' => $input]);
    }

    /**
     * Aplica el mismo cambio a varios productos.
     *
     * @param array<string,mixed> $input patch, productIds, orgId?
     * @return mixed
     */
    public function bulkUpdateProducts(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/products/bulk", ['body' => $input]);
    }

    /**
     * Cambia el precio de varios productos (o lo previsualiza).
     *
     * @param array<string,mixed> $input modo, valor, redondeo?, incluirVariantes?, soloVistaPrevia?, productIds, orgId?
     * @return mixed
     */
    public function bulkPriceProducts(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/products/bulk-price", ['body' => $input]);
    }

    /**
     * Elimina varios productos.
     *
     * @param array<string,mixed> $input productIds, orgId?
     * @return mixed
     */
    public function bulkDeleteProducts(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/products/bulk-delete", ['body' => $input]);
    }

    /** Detalle de un producto. @return mixed */
    public function getProduct(string $productId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/license-delivery/products/{$productId}");
    }

    /**
     * Edita un producto.
     *
     * @param array<string,mixed> $input name?, description?, sku?, category?, imageUrl?, priceCents?, durationDays?, renewalNoticeEnabled?, ... (ver `UpdateDigitalProductDto` en la API), orgId?
     * @return mixed
     */
    public function updateProduct(string $productId, array $input = [])
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/license-delivery/products/{$productId}", ['body' => $input]);
    }

    /** Elimina un producto. @return mixed */
    public function deleteProduct(string $productId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('DELETE', "/organizations/{$orgId}/license-delivery/products/{$productId}");
    }

    /**
     * Fija los precios del producto en otras monedas.
     *
     * @param array<string,mixed> $input precios, orgId?
     * @return mixed
     */
    public function setProductPrices(string $productId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PUT', "/organizations/{$orgId}/license-delivery/products/{$productId}/prices", ['body' => $input]);
    }

    /**
     * Sube la imagen del producto (PNG/JPG/WebP, máx 8 MB) y la sirve pública. (campo multipart `file`).
     *
     * @param string|\CURLFile $file ruta local o CURLFile
     * @return mixed
     */
    public function uploadProductImage(string $productId, $file, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/products/{$productId}/image", ['multipart' => ['file' => $file]]);
    }

    /** Quita la imagen subida del producto. @return mixed */
    public function removeProductImage(string $productId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('DELETE', "/organizations/{$orgId}/license-delivery/products/{$productId}/image");
    }

    /**
     * Crea un cobro (checkout) para vender el producto.
     *
     * @param array<string,mixed> $input variantId?, quantity?, method?, nequiPhone?, transferAccountId?, userLegalId?, userLegalIdType?, customerEmail?, ... (ver `CreateCheckoutDto` en la API), orgId?
     * @return mixed
     */
    public function checkout(string $productId, array $input = [])
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/products/{$productId}/checkout", ['body' => $input]);
    }

    // --- Llaves ---

    /**
     * Carga llaves al inventario del producto.
     *
     * @param array<string,mixed> $input keys, variantId?, orgId?
     * @return mixed
     */
    public function addKeys(string $productId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/products/{$productId}/keys", ['body' => $input]);
    }

    /**
     * Lista las llaves del producto.
     *
     * @param array<string,mixed> $query status?, orgId?
     * @return mixed
     */
    public function listKeys(string $productId, array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/license-delivery/products/{$productId}/keys", ['query' => $query]);
    }

    /** Elimina una llave del inventario. @return mixed */
    public function deleteKey(string $productId, string $keyId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('DELETE', "/organizations/{$orgId}/license-delivery/products/{$productId}/keys/{$keyId}");
    }

    /** Revoca una llave. @return mixed */
    public function revokeKey(string $productId, string $keyId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/products/{$productId}/keys/{$keyId}/revoke");
    }

    // --- Variantes ---

    /**
     * Crea una variante del producto.
     *
     * @param array<string,mixed> $input name, priceCents, durationDays?, sortOrder?, orgId?
     * @return mixed
     */
    public function createVariant(string $productId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/products/{$productId}/variants", ['body' => $input]);
    }

    /**
     * Edita una variante.
     *
     * @param array<string,mixed> $input name?, priceCents?, durationDays?, active?, sortOrder?, orgId?
     * @return mixed
     */
    public function updateVariant(string $productId, string $variantId, array $input = [])
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/license-delivery/products/{$productId}/variants/{$variantId}", ['body' => $input]);
    }

    /** Elimina una variante. @return mixed */
    public function deleteVariant(string $productId, string $variantId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('DELETE', "/organizations/{$orgId}/license-delivery/products/{$productId}/variants/{$variantId}");
    }

    // --- Venta desde una conversación ---

    /**
     * Calcula el carrito (precios, descuentos, stock) sin crear nada.
     *
     * @param array<string,mixed> $input items, currency?, orgId?
     * @return mixed
     */
    public function previewCart(string $conversationId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/conversations/{$conversationId}/cart-preview", ['body' => $input]);
    }

    /**
     * Vende un producto al cliente de la conversación.
     *
     * @param array<string,mixed> $input productId, variantId?, quantity?, method?, nequiPhone?, transferAccountId?, userLegalId?, userLegalIdType?, ... (ver `SellToConversationDto` en la API), orgId?
     * @return mixed
     */
    public function sellToConversation(string $conversationId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/conversations/{$conversationId}/sell", ['body' => $input]);
    }

    /**
     * Vende un carrito al cliente de la conversación.
     *
     * @param array<string,mixed> $input items, datoAdicionalDespues?, method?, nequiPhone?, transferAccountId?, customerEmail?, entregarYa?, enviarCobro?, notify?, currency?, orgId?
     * @return mixed
     */
    public function sellCartToConversation(string $conversationId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/conversations/{$conversationId}/sell-cart", ['body' => $input]);
    }

    /**
     * Entrega licencias a mano por la conversación.
     *
     * @param array<string,mixed> $input productId, variantId?, quantity?, orgId?
     * @return mixed
     */
    public function dispatch(string $conversationId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/conversations/{$conversationId}/dispatch", ['body' => $input]);
    }

    /**
     * Registra una venta hecha fuera del flujo (pagada o fiada).
     *
     * @param array<string,mixed> $input productId, variantId?, quantity?, entregar, pagado, unitPriceCents?, datoAdicional?, datoAdicionalDespues?, notify?, currency?, medio?, orgId?
     * @return mixed
     */
    public function registerManualSale(string $conversationId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/conversations/{$conversationId}/venta-manual", ['body' => $input]);
    }

    /**
     * Registra una venta manual de varios productos.
     *
     * @param array<string,mixed> $input items, datoAdicionalDespues?, entregar, pagado, notify?, currency?, medio?, orgId?
     * @return mixed
     */
    public function registerManualCartSale(string $conversationId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/conversations/{$conversationId}/venta-manual-carrito", ['body' => $input]);
    }

    // --- QR de la llave de transferencia ---

    /**
     * Sube el QR de la llave de transferencia (PNG/JPG/WebP). (campo multipart `file`).
     *
     * @param string|\CURLFile $file ruta local o CURLFile
     * @return mixed
     */
    public function uploadTransferQr($file, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/transfer-qr", ['multipart' => ['file' => $file]]);
    }

    /** Quita el QR de la llave de transferencia. @return mixed */
    public function removeTransferQr(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('DELETE', "/organizations/{$orgId}/license-delivery/transfer-qr");
    }

    // --- Renovaciones ---

    /** Estado de la plantilla de aviso de renovación. @return mixed */
    public function getRenewalTemplate(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/license-delivery/renewal-template");
    }

    /**
     * Crea la plantilla de aviso de renovación en Meta.
     *
     * @param array<string,mixed> $input wabaId?, name?, language?, bodyText?, orgId?
     * @return mixed
     */
    public function createRenewalTemplate(array $input = [])
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/renewal-template", ['body' => $input]);
    }

    /**
     * Licencias que vencen pronto (o ya vencidas).
     *
     * @param array<string,mixed> $query days?, includeExpired?, orgId?
     * @return mixed
     */
    public function listExpirations(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/license-delivery/expirations", ['query' => $query]);
    }

    // --- Pedidos ---

    /**
     * Lista los pedidos.
     *
     * @param array<string,mixed> $query status?, limit?, contactId?, conversationId?, orgId?
     * @return mixed
     */
    public function listOrders(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/license-delivery/orders", ['query' => $query]);
    }

    /** Detalle de un pedido. @return mixed */
    public function getOrder(string $orderId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/license-delivery/orders/{$orderId}");
    }

    /**
     * Cambia (o quita) la fecha de vencimiento del pedido.
     *
     * @param array<string,mixed> $input expiresAt?, orgId?
     * @return mixed
     */
    public function setOrderExpiration(string $orderId, array $input = [])
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/license-delivery/orders/{$orderId}/expiration", ['body' => $input]);
    }

    /** Reenvía la licencia al cliente. @return mixed */
    public function resendOrder(string $orderId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/orders/{$orderId}/resend");
    }

    /**
     * Reemplaza una llave entregada.
     *
     * @param array<string,mixed> $input source?, replacementValue?, reason?, notify?, orgId?
     * @return mixed
     */
    public function replaceKey(string $orderId, string $keyId, array $input = [])
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/orders/{$orderId}/keys/{$keyId}/replace", ['body' => $input]);
    }

    /** Cancela un pedido. @return mixed */
    public function cancelOrder(string $orderId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/orders/{$orderId}/cancel");
    }

    /**
     * Sube la captura del pago (PNG/JPG/WebP/PDF); devuelve su mediaAssetId para confirmarlo. (campo multipart `file`).
     *
     * @param string|\CURLFile $file ruta local o CURLFile
     * @return mixed
     */
    public function uploadPaymentCapture(string $orderId, $file, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/orders/{$orderId}/captura-de-pago", ['multipart' => ['file' => $file]]);
    }

    /**
     * Cambia el medio de pago del pedido.
     *
     * @param array<string,mixed> $input medio, nequiPhone?, avisarCliente?, orgId?
     * @return mixed
     */
    public function changePaymentMethod(string $orderId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/orders/{$orderId}/cambiar-medio", ['body' => $input]);
    }

    /**
     * Confirma a mano el pago por transferencia y entrega.
     *
     * @param array<string,mixed> $input medio?, comprobanteMediaId?, orgId?
     * @return mixed
     */
    public function confirmTransfer(string $orderId, array $input = [])
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/orders/{$orderId}/confirm-transfer", ['body' => $input]);
    }

    /**
     * Completa una entrega asistida con las llaves del asesor.
     *
     * @param array<string,mixed> $input llaves?, mensaje?, notificar?, orgId?
     * @return mixed
     */
    public function completeDelivery(string $orderId, array $input = [])
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/orders/{$orderId}/completar-entrega", ['body' => $input]);
    }

    /**
     * Reembolsa un pedido.
     *
     * @param array<string,mixed> $input reason?, orgId?
     * @return mixed
     */
    public function refundOrder(string $orderId, array $input = [])
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/orders/{$orderId}/refund", ['body' => $input]);
    }

    /**
     * Completa el dato adicional pendiente del pedido.
     *
     * @param array<string,mixed> $input valor, orgId?
     * @return mixed
     */
    public function completeExtraData(string $orderId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/license-delivery/orders/{$orderId}/dato-adicional", ['body' => $input]);
    }

    /**
     * Marca el pedido como pagado (sin entregar).
     *
     * @param array<string,mixed> $input medio?, comprobanteMediaId?, orgId?
     * @return mixed
     */
    public function markPaid(string $orderId, array $input = [])
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/orders/{$orderId}/marcar-pagada", ['body' => $input]);
    }

    /** Marca el pedido como entregado. @return mixed */
    public function markDelivered(string $orderId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/orders/{$orderId}/marcar-entregada");
    }

    /**
     * Envía el OTP de Daviplata del cliente.
     *
     * @param array<string,mixed> $input code, orgId?
     * @return mixed
     */
    public function submitOtp(string $orderId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/orders/{$orderId}/otp", ['body' => $input]);
    }

    /** Pide reenviar el OTP de Daviplata. @return mixed */
    public function resendOtp(string $orderId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/orders/{$orderId}/otp/resend");
    }

    /** Comprobantes de pago de un pedido. @return mixed */
    public function listOrderReceipts(string $orderId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/license-delivery/orders/{$orderId}/receipts");
    }

    // --- Bandeja de avisos ---

    /** Prueba la conexión a la bandeja y muestra lo que lee. @return mixed */
    public function testInbox(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/inbox/test");
    }

    /** Correos donde llegan los avisos de pago. @return mixed */
    public function listMailboxes(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/license-delivery/buzones");
    }

    /**
     * Agrega un buzón de avisos.
     *
     * @param array<string,mixed> $input host?, port?, user?, password?, tls?, processedFolder?, deleteAfterRead?, llaveIds?, orgId?
     * @return mixed
     */
    public function createMailbox(array $input = [])
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/buzones", ['body' => $input]);
    }

    /**
     * Edita un buzón de avisos.
     *
     * @param array<string,mixed> $input host?, port?, user?, password?, tls?, processedFolder?, deleteAfterRead?, llaveIds?, orgId?
     * @return mixed
     */
    public function updateMailbox(string $buzonId, array $input = [])
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/license-delivery/buzones/{$buzonId}", ['body' => $input]);
    }

    /** Elimina un buzón de avisos. @return mixed */
    public function deleteMailbox(string $buzonId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('DELETE', "/organizations/{$orgId}/license-delivery/buzones/{$buzonId}");
    }

    /** Prueba la conexión de un buzón. @return mixed */
    public function testMailbox(string $buzonId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/buzones/{$buzonId}/probar");
    }

    /** Pagos leídos de los avisos del banco. @return mixed */
    public function listInboxPayments(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/license-delivery/inbox/payments");
    }

    /**
     * Concilia un pago con un pedido.
     *
     * @param array<string,mixed> $input orderId, orgId?
     * @return mixed
     */
    public function matchPayment(string $pagoId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/inbox/payments/{$pagoId}/match", ['body' => $input]);
    }

    /**
     * Concilia un pago con un cobro agrupado.
     *
     * @param array<string,mixed> $input cobroId, orgId?
     * @return mixed
     */
    public function matchPaymentToCharge(string $pagoId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/inbox/payments/{$pagoId}/match-cobro", ['body' => $input]);
    }

    /** Pedidos a los que se puede mover un pago. @return mixed */
    public function getReassignOptions(string $pagoId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/license-delivery/inbox/payments/{$pagoId}/reassign-options");
    }

    /**
     * Mueve un pago a otro pedido.
     *
     * @param array<string,mixed> $input orderId, nota?, orgId?
     * @return mixed
     */
    public function reassignPayment(string $pagoId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/inbox/payments/{$pagoId}/reassign", ['body' => $input]);
    }

    /**
     * Ignora un pago.
     *
     * @param array<string,mixed> $input nota?, orgId?
     * @return mixed
     */
    public function ignorePayment(string $pagoId, array $input = [])
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/inbox/payments/{$pagoId}/ignore", ['body' => $input]);
    }

    // --- Bandeja de pagos ---

    /**
     * Bandeja unificada de pagos (avisos del banco y comprobantes).
     *
     * @param array<string,mixed> $query origen?, estado?, desde?, hasta?, q?, limit?, offset?, orgId?
     * @return mixed
     */
    public function paymentsInbox(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/license-delivery/payments-inbox", ['query' => $query]);
    }

    /**
     * Descarta varios pagos/comprobantes (hasta 100).
     *
     * @param array<string,mixed> $input items, reason, orgId?
     * @return mixed
     */
    public function discardPayments(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/payments-inbox/discard", ['body' => $input]);
    }

    // --- Cobros agrupados ---

    /**
     * Pedidos que el contacto debe (fiados y esperando pago).
     *
     * @param array<string,mixed> $query contactId, orgId?
     * @return mixed
     */
    public function listOrdersToCharge(array $query)
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/license-delivery/cobros-agrupados/pendientes", ['query' => $query]);
    }

    /**
     * Lista los cobros agrupados.
     *
     * @param array<string,mixed> $query contactId?, status?, orgId?
     * @return mixed
     */
    public function listGroupedCharges(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/license-delivery/cobros-agrupados", ['query' => $query]);
    }

    /**
     * Crea un cobro agrupado de varios pedidos.
     *
     * @param array<string,mixed> $input contactId, orderIds?, conversationId?, medio?, orgId?
     * @return mixed
     */
    public function createGroupedCharge(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/cobros-agrupados", ['body' => $input]);
    }

    /** Reenvía el cobro al cliente. @return mixed */
    public function resendGroupedCharge(string $cobroId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/cobros-agrupados/{$cobroId}/reenviar");
    }

    /**
     * Anula un cobro agrupado.
     *
     * @param array<string,mixed> $input motivo?, orgId?
     * @return mixed
     */
    public function voidGroupedCharge(string $cobroId, array $input = [])
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/cobros-agrupados/{$cobroId}/anular", ['body' => $input]);
    }

    /**
     * Marca un cobro agrupado como pagado (total o parcial).
     *
     * @param array<string,mixed> $input orderIds?, medio?, avisar?, orgId?
     * @return mixed
     */
    public function markGroupedChargePaid(string $cobroId, array $input = [])
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/cobros-agrupados/{$cobroId}/marcar-pagado", ['body' => $input]);
    }

    /**
     * Registra un pago que cubre varios pedidos, sin cobro previo.
     *
     * @param array<string,mixed> $input orderIds, medio?, avisar?, contactId, conversationId?, orgId?
     * @return mixed
     */
    public function registerJointPayment(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/cobros-agrupados/pago-registrado", ['body' => $input]);
    }

    // --- Cobros libres ---

    /**
     * Crea un cobro libre (monto y concepto a mano).
     *
     * @param array<string,mixed> $input contactId, conversationId?, concepto?, montoCents?, invoiceId?, perfilDeCobroId?, orgId?
     * @return mixed
     */
    public function createFreeCharge(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/cobros-libres", ['body' => $input]);
    }

    /**
     * Facturas de la plataforma Mosend del contacto (solo la org que opera Mosend).
     *
     * @param array<string,mixed> $query contactId, orgId?
     * @return mixed
     */
    public function listMosendInvoices(array $query)
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/license-delivery/cobros-libres/facturas-mosend", ['query' => $query]);
    }

    // --- Perfiles de cobro (marcas) ---

    /** Lista las marcas que cobran. @return mixed */
    public function listBillingProfiles(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/license-delivery/perfiles-de-cobro");
    }

    /**
     * Crea una marca.
     *
     * @param array<string,mixed> $input nombre?, llaveId?, alPagar?, aceptaWompi?, aceptaBinance?, phoneNumberIds?, ordenDeMedios?, wompiEnv?, wompiPublicKey?, wompiPrivateKey?, wompiEventsSecret?, wompiIntegritySecret?, orgId?
     * @return mixed
     */
    public function createBillingProfile(array $input = [])
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/perfiles-de-cobro", ['body' => $input]);
    }

    /**
     * Edita una marca.
     *
     * @param array<string,mixed> $input nombre?, llaveId?, alPagar?, aceptaWompi?, aceptaBinance?, phoneNumberIds?, ordenDeMedios?, wompiEnv?, wompiPublicKey?, wompiPrivateKey?, wompiEventsSecret?, wompiIntegritySecret?, orgId?
     * @return mixed
     */
    public function updateBillingProfile(string $perfilId, array $input = [])
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/license-delivery/perfiles-de-cobro/{$perfilId}", ['body' => $input]);
    }

    /** Elimina una marca. @return mixed */
    public function deleteBillingProfile(string $perfilId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('DELETE', "/organizations/{$orgId}/license-delivery/perfiles-de-cobro/{$perfilId}");
    }

    /**
     * Resumen de ventas por marca.
     *
     * @param array<string,mixed> $query dias?, desde?, hasta?, orgId?
     * @return mixed
     */
    public function billingProfilesSummary(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/license-delivery/perfiles-de-cobro/resumen", ['query' => $query]);
    }

    // --- Cuentas de transferencia ---

    /** Lista las cuentas de transferencia. @return mixed */
    public function listTransferAccounts(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/license-delivery/transfer-accounts");
    }

    /**
     * Crea una cuenta de transferencia.
     *
     * @param array<string,mixed> $input label, bankName?, accountKind?, accountNumber, holder?, holderDoc?, instructions?, qrMediaId?, ... (ver `CuentaDeTransferenciaDto` en la API), orgId?
     * @return mixed
     */
    public function createTransferAccount(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/transfer-accounts", ['body' => $input]);
    }

    /**
     * Edita una cuenta de transferencia.
     *
     * @param array<string,mixed> $input label?, bankName?, accountKind?, accountNumber?, holder?, holderDoc?, instructions?, qrMediaId?, ... (ver `EditarCuentaDeTransferenciaDto` en la API), orgId?
     * @return mixed
     */
    public function updateTransferAccount(string $accountId, array $input = [])
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/license-delivery/transfer-accounts/{$accountId}", ['body' => $input]);
    }

    /** Elimina una cuenta de transferencia. @return mixed */
    public function deleteTransferAccount(string $accountId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('DELETE', "/organizations/{$orgId}/license-delivery/transfer-accounts/{$accountId}");
    }

    /**
     * Reordena las cuentas (se envían todas, en orden).
     *
     * @param array<string,mixed> $input ids, orgId?
     * @return mixed
     */
    public function reorderTransferAccounts(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/transfer-accounts/reorder", ['body' => $input]);
    }

    // --- Llaves de cobro ---

    /** Lista las llaves de cobro (Bre-B / Nequi). @return mixed */
    public function listTransferKeys(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/license-delivery/transfer-keys");
    }

    /**
     * Crea una llave de cobro.
     *
     * @param array<string,mixed> $input label, entity?, key, holder?, instructions?, senderDomain?, forwarderAddress?, autoApprove?, buzonId?, orgId?
     * @return mixed
     */
    public function createTransferKey(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/transfer-keys", ['body' => $input]);
    }

    /**
     * Edita una llave de cobro.
     *
     * @param array<string,mixed> $input label?, entity?, key?, holder?, instructions?, senderDomain?, forwarderAddress?, autoApprove?, buzonId?, orgId?
     * @return mixed
     */
    public function updateTransferKey(string $keyId, array $input = [])
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/license-delivery/transfer-keys/{$keyId}", ['body' => $input]);
    }

    /** Elimina una llave de cobro. @return mixed */
    public function deleteTransferKey(string $keyId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('DELETE', "/organizations/{$orgId}/license-delivery/transfer-keys/{$keyId}");
    }

    /** Deja activa esta llave. @return mixed */
    public function activateTransferKey(string $keyId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/transfer-keys/{$keyId}/activate");
    }

    /**
     * Sube el QR de una llave (PNG/JPG/WebP). (campo multipart `file`).
     *
     * @param string|\CURLFile $file ruta local o CURLFile
     * @return mixed
     */
    public function uploadTransferKeyQr(string $keyId, $file, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/transfer-keys/{$keyId}/qr", ['multipart' => ['file' => $file]]);
    }

    /** Quita el QR de una llave. @return mixed */
    public function removeTransferKeyQr(string $keyId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('DELETE', "/organizations/{$orgId}/license-delivery/transfer-keys/{$keyId}/qr");
    }

    // --- Comprobantes ---

    /**
     * Comprobantes de pago pendientes de revisión.
     *
     * @param array<string,mixed> $query limit?, orgId?
     * @return mixed
     */
    public function listPendingReceipts(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/license-delivery/receipts", ['query' => $query]);
    }

    /**
     * Aprueba un comprobante.
     *
     * @param array<string,mixed> $input note?, orgId?
     * @return mixed
     */
    public function approveReceipt(string $receiptId, array $input = [])
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/receipts/{$receiptId}/approve", ['body' => $input]);
    }

    /**
     * Rechaza un comprobante (el cliente lee el motivo).
     *
     * @param array<string,mixed> $input reason, orgId?
     * @return mixed
     */
    public function rejectReceipt(string $receiptId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/license-delivery/receipts/{$receiptId}/reject", ['body' => $input]);
    }

    // --- Imagen pública ---

    /**
     * URL pública de la imagen del producto (`GET /license-delivery/img/{productId}`, sin
     * auth). Devuelve la URL en vez de descargarla porque la respuesta es binaria.
     */
    public function productImageUrl(string $productId, string $baseUrl = 'https://api.mosend.dev'): string
    {
        return rtrim($baseUrl, '/') . '/license-delivery/img/' . rawurlencode($productId);
    }
}
