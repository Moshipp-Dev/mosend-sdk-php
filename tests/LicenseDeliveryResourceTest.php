<?php

declare(strict_types=1);

namespace Mosend\Tests;

use Mosend\Exception\MosendValidationException;
use Mosend\Resources\LicenseDeliveryResource;
use Mosend\Tests\Support\RecordingHttpClient;
use PHPUnit\Framework\TestCase;

/** Paths, métodos y cuerpos de `/organizations/{orgId}/license-delivery` verificados contra el backend. */
final class LicenseDeliveryResourceTest extends TestCase
{
    private const ORG = 'org-1';

    private function path(RecordingHttpClient $r): string
    {
        return parse_url($r->lastCall()['url'], PHP_URL_PATH) ?: '';
    }

    private function query(RecordingHttpClient $r): string
    {
        return (string) parse_url($r->lastCall()['url'], PHP_URL_QUERY);
    }

    /** @return array<string,array{0:callable,1:string,2:string,3:array<string,mixed>|null,4:string|null,5:bool}> */
    public function operations(): array
    {
        $file = __FILE__;
        return [
            'getSettings' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->getSettings(); },
                'GET', '/settings', null, null, false,
            ],
            'updateSettings' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->updateSettings(['enabled' => true]); },
                'PUT', '/settings', ['enabled' => true], null, false,
            ],
            'setPaidAddon' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->setPaidAddon(['enabled' => true]); },
                'PUT', '/settings/addon', ['enabled' => true], null, false,
            ],
            'sellingReadiness' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->sellingReadiness(['phoneNumberId' => 'p-1']); },
                'GET', '/selling-readiness', null, 'phoneNumberId=p-1', false,
            ],
            'analytics' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->analytics(['days' => 30]); },
                'GET', '/analytics', null, 'days=30', false,
            ],
            'listCoupons' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->listCoupons(); },
                'GET', '/coupons', null, null, false,
            ],
            'createCoupon' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->createCoupon(['code' => 'x', 'discountValue' => 1]); },
                'POST', '/coupons', ['code' => 'x', 'discountValue' => 1], null, false,
            ],
            'updateCoupon' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->updateCoupon('cou-1', ['code' => 'x']); },
                'PATCH', '/coupons/cou-1', ['code' => 'x'], null, false,
            ],
            'deleteCoupon' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->deleteCoupon('cou-1'); },
                'DELETE', '/coupons/cou-1', null, null, false,
            ],
            'listPriceLists' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->listPriceLists(); },
                'GET', '/price-lists', null, null, false,
            ],
            'createPriceList' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->createPriceList(['name' => 'x']); },
                'POST', '/price-lists', ['name' => 'x'], null, false,
            ],
            'updatePriceList' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->updatePriceList('pri-1', ['name' => 'x']); },
                'PATCH', '/price-lists/pri-1', ['name' => 'x'], null, false,
            ],
            'deletePriceList' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->deletePriceList('pri-1'); },
                'DELETE', '/price-lists/pri-1', null, null, false,
            ],
            'setPriceListItem' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->setPriceListItem('pri-1', ['productId' => 'x']); },
                'PUT', '/price-lists/pri-1/items', ['productId' => 'x'], null, false,
            ],
            'bulkSetPriceListItems' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->bulkSetPriceListItems('pri-1', ['productIds' => ['x'], 'modo' => 'PORCENTAJE_DEL_BASE']); },
                'PUT', '/price-lists/pri-1/items/bulk', ['productIds' => ['x'], 'modo' => 'PORCENTAJE_DEL_BASE'], null, false,
            ],
            'listProducts' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->listProducts(['contactId' => 'c-1']); },
                'GET', '/products', null, 'contactId=c-1', false,
            ],
            'createProduct' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->createProduct(['name' => 'x', 'priceCents' => 1]); },
                'POST', '/products', ['name' => 'x', 'priceCents' => 1], null, false,
            ],
            'importProducts' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->importProducts(['products' => [['name' => 'x', 'priceCents' => 1]]]); },
                'POST', '/products/import', ['products' => [['name' => 'x', 'priceCents' => 1]]], null, false,
            ],
            'bulkUpdateProducts' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->bulkUpdateProducts(['patch' => ['active' => true], 'productIds' => ['x']]); },
                'POST', '/products/bulk', ['patch' => ['active' => true], 'productIds' => ['x']], null, false,
            ],
            'bulkPriceProducts' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->bulkPriceProducts(['modo' => 'PORCENTAJE', 'valor' => 1, 'productIds' => ['x']]); },
                'POST', '/products/bulk-price', ['modo' => 'PORCENTAJE', 'valor' => 1, 'productIds' => ['x']], null, false,
            ],
            'bulkDeleteProducts' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->bulkDeleteProducts(['productIds' => ['x']]); },
                'POST', '/products/bulk-delete', ['productIds' => ['x']], null, false,
            ],
            'getProduct' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->getProduct('pro-1'); },
                'GET', '/products/pro-1', null, null, false,
            ],
            'updateProduct' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->updateProduct('pro-1', ['name' => 'x']); },
                'PATCH', '/products/pro-1', ['name' => 'x'], null, false,
            ],
            'deleteProduct' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->deleteProduct('pro-1'); },
                'DELETE', '/products/pro-1', null, null, false,
            ],
            'setProductPrices' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->setProductPrices('pro-1', ['precios' => [['currency' => 'COP', 'priceCents' => 1]]]); },
                'PUT', '/products/pro-1/prices', ['precios' => [['currency' => 'COP', 'priceCents' => 1]]], null, false,
            ],
            'uploadProductImage' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->uploadProductImage('pro-1', $file); },
                'POST', '/products/pro-1/image', null, null, true,
            ],
            'removeProductImage' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->removeProductImage('pro-1'); },
                'DELETE', '/products/pro-1/image', null, null, false,
            ],
            'checkout' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->checkout('pro-1', ['variantId' => 'x']); },
                'POST', '/products/pro-1/checkout', ['variantId' => 'x'], null, false,
            ],
            'addKeys' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->addKeys('pro-1', ['keys' => ['x']]); },
                'POST', '/products/pro-1/keys', ['keys' => ['x']], null, false,
            ],
            'listKeys' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->listKeys('pro-1', ['status' => 'AVAILABLE']); },
                'GET', '/products/pro-1/keys', null, 'status=AVAILABLE', false,
            ],
            'deleteKey' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->deleteKey('pro-1', 'key-1'); },
                'DELETE', '/products/pro-1/keys/key-1', null, null, false,
            ],
            'revokeKey' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->revokeKey('pro-1', 'key-1'); },
                'POST', '/products/pro-1/keys/key-1/revoke', null, null, false,
            ],
            'createVariant' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->createVariant('pro-1', ['name' => 'x', 'priceCents' => 1]); },
                'POST', '/products/pro-1/variants', ['name' => 'x', 'priceCents' => 1], null, false,
            ],
            'updateVariant' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->updateVariant('pro-1', 'var-1', ['name' => 'x']); },
                'PATCH', '/products/pro-1/variants/var-1', ['name' => 'x'], null, false,
            ],
            'deleteVariant' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->deleteVariant('pro-1', 'var-1'); },
                'DELETE', '/products/pro-1/variants/var-1', null, null, false,
            ],
            'previewCart' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->previewCart('con-1', ['items' => [['productId' => 'x']]]); },
                'POST', '/conversations/con-1/cart-preview', ['items' => [['productId' => 'x']]], null, false,
            ],
            'sellToConversation' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->sellToConversation('con-1', ['productId' => 'x']); },
                'POST', '/conversations/con-1/sell', ['productId' => 'x'], null, false,
            ],
            'sellCartToConversation' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->sellCartToConversation('con-1', ['items' => [['productId' => 'x']]]); },
                'POST', '/conversations/con-1/sell-cart', ['items' => [['productId' => 'x']]], null, false,
            ],
            'dispatch' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->dispatch('con-1', ['productId' => 'x']); },
                'POST', '/conversations/con-1/dispatch', ['productId' => 'x'], null, false,
            ],
            'registerManualSale' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->registerManualSale('con-1', ['productId' => 'x', 'entregar' => true, 'pagado' => true]); },
                'POST', '/conversations/con-1/venta-manual', ['productId' => 'x', 'entregar' => true, 'pagado' => true], null, false,
            ],
            'registerManualCartSale' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->registerManualCartSale('con-1', ['items' => [['productId' => 'x']], 'entregar' => true, 'pagado' => true]); },
                'POST', '/conversations/con-1/venta-manual-carrito', ['items' => [['productId' => 'x']], 'entregar' => true, 'pagado' => true], null, false,
            ],
            'uploadTransferQr' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->uploadTransferQr($file); },
                'POST', '/transfer-qr', null, null, true,
            ],
            'removeTransferQr' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->removeTransferQr(); },
                'DELETE', '/transfer-qr', null, null, false,
            ],
            'getRenewalTemplate' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->getRenewalTemplate(); },
                'GET', '/renewal-template', null, null, false,
            ],
            'createRenewalTemplate' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->createRenewalTemplate(['wabaId' => 'x']); },
                'POST', '/renewal-template', ['wabaId' => 'x'], null, false,
            ],
            'listExpirations' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->listExpirations(['days' => 15, 'includeExpired' => true]); },
                'GET', '/expirations', null, 'days=15&includeExpired=true', false,
            ],
            'listOrders' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->listOrders(['status' => 'PAID', 'limit' => 10]); },
                'GET', '/orders', null, 'status=PAID&limit=10', false,
            ],
            'getOrder' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->getOrder('ord-1'); },
                'GET', '/orders/ord-1', null, null, false,
            ],
            'setOrderExpiration' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->setOrderExpiration('ord-1', ['expiresAt' => 'x']); },
                'PATCH', '/orders/ord-1/expiration', ['expiresAt' => 'x'], null, false,
            ],
            'resendOrder' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->resendOrder('ord-1'); },
                'POST', '/orders/ord-1/resend', null, null, false,
            ],
            'replaceKey' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->replaceKey('ord-1', 'key-1', ['source' => 'inventory']); },
                'POST', '/orders/ord-1/keys/key-1/replace', ['source' => 'inventory'], null, false,
            ],
            'cancelOrder' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->cancelOrder('ord-1'); },
                'POST', '/orders/ord-1/cancel', null, null, false,
            ],
            'uploadPaymentCapture' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->uploadPaymentCapture('ord-1', $file); },
                'POST', '/orders/ord-1/captura-de-pago', null, null, true,
            ],
            'changePaymentMethod' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->changePaymentMethod('ord-1', ['medio' => 'x']); },
                'POST', '/orders/ord-1/cambiar-medio', ['medio' => 'x'], null, false,
            ],
            'confirmTransfer' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->confirmTransfer('ord-1', ['medio' => 'x']); },
                'POST', '/orders/ord-1/confirm-transfer', ['medio' => 'x'], null, false,
            ],
            'completeDelivery' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->completeDelivery('ord-1', ['llaves' => ['x']]); },
                'POST', '/orders/ord-1/completar-entrega', ['llaves' => ['x']], null, false,
            ],
            'refundOrder' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->refundOrder('ord-1', ['reason' => 'x']); },
                'POST', '/orders/ord-1/refund', ['reason' => 'x'], null, false,
            ],
            'completeExtraData' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->completeExtraData('ord-1', ['valor' => 'x']); },
                'PATCH', '/orders/ord-1/dato-adicional', ['valor' => 'x'], null, false,
            ],
            'markPaid' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->markPaid('ord-1', ['medio' => 'x']); },
                'POST', '/orders/ord-1/marcar-pagada', ['medio' => 'x'], null, false,
            ],
            'markDelivered' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->markDelivered('ord-1'); },
                'POST', '/orders/ord-1/marcar-entregada', null, null, false,
            ],
            'submitOtp' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->submitOtp('ord-1', ['code' => 'x']); },
                'POST', '/orders/ord-1/otp', ['code' => 'x'], null, false,
            ],
            'resendOtp' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->resendOtp('ord-1'); },
                'POST', '/orders/ord-1/otp/resend', null, null, false,
            ],
            'listOrderReceipts' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->listOrderReceipts('ord-1'); },
                'GET', '/orders/ord-1/receipts', null, null, false,
            ],
            'testInbox' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->testInbox(); },
                'POST', '/inbox/test', null, null, false,
            ],
            'listMailboxes' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->listMailboxes(); },
                'GET', '/buzones', null, null, false,
            ],
            'createMailbox' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->createMailbox(['host' => 'x']); },
                'POST', '/buzones', ['host' => 'x'], null, false,
            ],
            'updateMailbox' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->updateMailbox('buz-1', ['host' => 'x']); },
                'PATCH', '/buzones/buz-1', ['host' => 'x'], null, false,
            ],
            'deleteMailbox' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->deleteMailbox('buz-1'); },
                'DELETE', '/buzones/buz-1', null, null, false,
            ],
            'testMailbox' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->testMailbox('buz-1'); },
                'POST', '/buzones/buz-1/probar', null, null, false,
            ],
            'listInboxPayments' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->listInboxPayments(); },
                'GET', '/inbox/payments', null, null, false,
            ],
            'matchPayment' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->matchPayment('pag-1', ['orderId' => 'x']); },
                'POST', '/inbox/payments/pag-1/match', ['orderId' => 'x'], null, false,
            ],
            'matchPaymentToCharge' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->matchPaymentToCharge('pag-1', ['cobroId' => 'x']); },
                'POST', '/inbox/payments/pag-1/match-cobro', ['cobroId' => 'x'], null, false,
            ],
            'getReassignOptions' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->getReassignOptions('pag-1'); },
                'GET', '/inbox/payments/pag-1/reassign-options', null, null, false,
            ],
            'reassignPayment' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->reassignPayment('pag-1', ['orderId' => 'x']); },
                'POST', '/inbox/payments/pag-1/reassign', ['orderId' => 'x'], null, false,
            ],
            'ignorePayment' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->ignorePayment('pag-1', ['nota' => 'x']); },
                'POST', '/inbox/payments/pag-1/ignore', ['nota' => 'x'], null, false,
            ],
            'paymentsInbox' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->paymentsInbox(['origen' => 'BANCO', 'q' => 'juan']); },
                'GET', '/payments-inbox', null, 'origen=BANCO&q=juan', false,
            ],
            'discardPayments' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->discardPayments(['items' => [['id' => 'i-1', 'origen' => 'BANCO']], 'reason' => 'x']); },
                'POST', '/payments-inbox/discard', ['items' => [['id' => 'i-1', 'origen' => 'BANCO']], 'reason' => 'x'], null, false,
            ],
            'listOrdersToCharge' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->listOrdersToCharge(['contactId' => 'c-1']); },
                'GET', '/cobros-agrupados/pendientes', null, 'contactId=c-1', false,
            ],
            'listGroupedCharges' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->listGroupedCharges(['status' => 'PENDIENTE']); },
                'GET', '/cobros-agrupados', null, 'status=PENDIENTE', false,
            ],
            'createGroupedCharge' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->createGroupedCharge(['contactId' => 'x']); },
                'POST', '/cobros-agrupados', ['contactId' => 'x'], null, false,
            ],
            'resendGroupedCharge' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->resendGroupedCharge('cob-1'); },
                'POST', '/cobros-agrupados/cob-1/reenviar', null, null, false,
            ],
            'voidGroupedCharge' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->voidGroupedCharge('cob-1', ['motivo' => 'x']); },
                'POST', '/cobros-agrupados/cob-1/anular', ['motivo' => 'x'], null, false,
            ],
            'markGroupedChargePaid' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->markGroupedChargePaid('cob-1', ['orderIds' => ['x']]); },
                'POST', '/cobros-agrupados/cob-1/marcar-pagado', ['orderIds' => ['x']], null, false,
            ],
            'registerJointPayment' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->registerJointPayment(['orderIds' => ['x'], 'contactId' => 'x']); },
                'POST', '/cobros-agrupados/pago-registrado', ['orderIds' => ['x'], 'contactId' => 'x'], null, false,
            ],
            'createFreeCharge' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->createFreeCharge(['contactId' => 'x']); },
                'POST', '/cobros-libres', ['contactId' => 'x'], null, false,
            ],
            'listMosendInvoices' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->listMosendInvoices(['contactId' => 'c-1']); },
                'GET', '/cobros-libres/facturas-mosend', null, 'contactId=c-1', false,
            ],
            'listBillingProfiles' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->listBillingProfiles(); },
                'GET', '/perfiles-de-cobro', null, null, false,
            ],
            'createBillingProfile' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->createBillingProfile(['nombre' => 'x']); },
                'POST', '/perfiles-de-cobro', ['nombre' => 'x'], null, false,
            ],
            'updateBillingProfile' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->updateBillingProfile('per-1', ['nombre' => 'x']); },
                'PATCH', '/perfiles-de-cobro/per-1', ['nombre' => 'x'], null, false,
            ],
            'deleteBillingProfile' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->deleteBillingProfile('per-1'); },
                'DELETE', '/perfiles-de-cobro/per-1', null, null, false,
            ],
            'billingProfilesSummary' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->billingProfilesSummary(['dias' => 7]); },
                'GET', '/perfiles-de-cobro/resumen', null, 'dias=7', false,
            ],
            'listTransferAccounts' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->listTransferAccounts(); },
                'GET', '/transfer-accounts', null, null, false,
            ],
            'createTransferAccount' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->createTransferAccount(['label' => 'x', 'accountNumber' => 'x']); },
                'POST', '/transfer-accounts', ['label' => 'x', 'accountNumber' => 'x'], null, false,
            ],
            'updateTransferAccount' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->updateTransferAccount('acc-1', ['label' => 'x']); },
                'PATCH', '/transfer-accounts/acc-1', ['label' => 'x'], null, false,
            ],
            'deleteTransferAccount' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->deleteTransferAccount('acc-1'); },
                'DELETE', '/transfer-accounts/acc-1', null, null, false,
            ],
            'reorderTransferAccounts' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->reorderTransferAccounts(['ids' => ['x']]); },
                'POST', '/transfer-accounts/reorder', ['ids' => ['x']], null, false,
            ],
            'listTransferKeys' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->listTransferKeys(); },
                'GET', '/transfer-keys', null, null, false,
            ],
            'createTransferKey' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->createTransferKey(['label' => 'x', 'key' => 'x']); },
                'POST', '/transfer-keys', ['label' => 'x', 'key' => 'x'], null, false,
            ],
            'updateTransferKey' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->updateTransferKey('key-1', ['label' => 'x']); },
                'PATCH', '/transfer-keys/key-1', ['label' => 'x'], null, false,
            ],
            'deleteTransferKey' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->deleteTransferKey('key-1'); },
                'DELETE', '/transfer-keys/key-1', null, null, false,
            ],
            'activateTransferKey' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->activateTransferKey('key-1'); },
                'POST', '/transfer-keys/key-1/activate', null, null, false,
            ],
            'uploadTransferKeyQr' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->uploadTransferKeyQr('key-1', $file); },
                'POST', '/transfer-keys/key-1/qr', null, null, true,
            ],
            'removeTransferKeyQr' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->removeTransferKeyQr('key-1'); },
                'DELETE', '/transfer-keys/key-1/qr', null, null, false,
            ],
            'listPendingReceipts' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->listPendingReceipts(['limit' => 20]); },
                'GET', '/receipts', null, 'limit=20', false,
            ],
            'approveReceipt' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->approveReceipt('rec-1', ['note' => 'x']); },
                'POST', '/receipts/rec-1/approve', ['note' => 'x'], null, false,
            ],
            'rejectReceipt' => [
                static function (LicenseDeliveryResource $ld) use ($file) { return $ld->rejectReceipt('rec-1', ['reason' => 'x']); },
                'POST', '/receipts/rec-1/reject', ['reason' => 'x'], null, false,
            ],
        ];
    }

    public function testCubreTodasLasOperaciones(): void
    {
        self::assertCount(106, $this->operations());
    }

    /**
     * @dataProvider operations
     * @param array<string,mixed>|null $body
     */
    public function testOperacion(callable $call, string $method, string $sub, ?array $body, ?string $query, bool $multipart): void
    {
        $r = new RecordingHttpClient();
        $call(new LicenseDeliveryResource($r, self::ORG));
        self::assertSame($method, $r->lastCall()['method']);
        self::assertSame('/organizations/org-1/license-delivery' . $sub, $this->path($r));
        if ($body !== null) {
            self::assertSame($body, $r->lastBody());
        }
        if ($query !== null) {
            self::assertSame($query, $this->query($r));
        }
        self::assertSame($multipart, $r->lastCall()['multipart']);
    }

    public function testOrgIdPorLlamadaNoViajaEnElBody(): void
    {
        $r = new RecordingHttpClient();
        (new LicenseDeliveryResource($r, self::ORG))->createCoupon(['code' => 'X', 'discountValue' => 10, 'orgId' => 'org-2']);
        self::assertSame('/organizations/org-2/license-delivery/coupons', $this->path($r));
        self::assertSame(['code' => 'X', 'discountValue' => 10], $r->lastBody());
    }

    public function testSinOrgIdLanza(): void
    {
        $this->expectException(MosendValidationException::class);
        (new LicenseDeliveryResource(new RecordingHttpClient()))->listOrders();
    }

    public function testProductImageUrl(): void
    {
        $ld = new LicenseDeliveryResource(new RecordingHttpClient(), self::ORG);
        self::assertSame('https://api.mosend.dev/license-delivery/img/p-1', $ld->productImageUrl('p-1'));
        self::assertSame('http://localhost:3000/license-delivery/img/p-1', $ld->productImageUrl('p-1', 'http://localhost:3000/'));
    }
}
