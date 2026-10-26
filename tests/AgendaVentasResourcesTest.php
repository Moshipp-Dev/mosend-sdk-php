<?php

declare(strict_types=1);

namespace Mosend\Tests;

use Mosend\Resources\AgendaResource;
use Mosend\Resources\CatalogsResource;
use Mosend\Resources\MercadoPagoResource;
use Mosend\Resources\NequiPaymentsResource;
use Mosend\Resources\OrdersResource;
use Mosend\Resources\PaymentReportsResource;
use Mosend\Resources\QuotesResource;
use Mosend\Resources\SalesResource;
use Mosend\Resources\WompiResource;
use Mosend\Tests\Support\RecordingHttpClient;
use PHPUnit\Framework\TestCase;

/**
 * Agenda, Ventas, Cotizaciones y cobros: método + path de cada operación,
 * verificados contra los controladores del backend.
 */
final class AgendaVentasResourcesTest extends TestCase
{
    private const ORG = 'org-1';

    /**
     * Ejecuta cada llamada y compara [método, path] en orden.
     *
     * @param array<int,array{0:callable,1:string,2:string}> $cases
     */
    private function assertCalls(RecordingHttpClient $r, array $cases): void
    {
        foreach ($cases as $i => [$call, $method, $path]) {
            $call();
            $last = $r->lastCall();
            self::assertSame($method, $last['method'], "caso {$i} ({$path})");
            self::assertSame($path, parse_url($last['url'], PHP_URL_PATH), "caso {$i}");
        }
    }

    /** @return array<string,mixed> */
    private function query(RecordingHttpClient $r): array
    {
        $q = parse_url($r->lastCall()['url'], PHP_URL_QUERY);
        $out = [];
        parse_str(is_string($q) ? $q : '', $out);
        return $out;
    }

    public function testAgenda(): void
    {
        $r = new RecordingHttpClient();
        $a = new AgendaResource($r, self::ORG);
        $b = '/organizations/org-1/agenda';
        $this->assertCalls($r, [
            [function () use ($a) { $a->getSettings(); }, 'GET', "{$b}/settings"],
            [function () use ($a) { $a->updateSettings(['enabled' => true]); }, 'PATCH', "{$b}/settings"],
            [function () use ($a) { $a->applyPreset(['tipo' => 'salon']); }, 'POST', "{$b}/preset"],
            [function () use ($a) { $a->getIcs(); }, 'GET', "{$b}/ics"],
            [function () use ($a) { $a->rotateIcs(); }, 'POST', "{$b}/ics/rotate"],
            [function () use ($a) { $a->revokeIcs(); }, 'DELETE', "{$b}/ics"],
            [function () use ($a) { $a->listTypes(); }, 'GET', "{$b}/types"],
            [function () use ($a) { $a->createType(['name' => 'Corte', 'durationMin' => 30]); }, 'POST', "{$b}/types"],
            [function () use ($a) { $a->updateType('t1', ['active' => false]); }, 'PATCH', "{$b}/types/t1"],
            [function () use ($a) { $a->contactSummary('c1'); }, 'GET', "{$b}/contacts/c1/resumen"],
            [function () use ($a) { $a->listResources(); }, 'GET', "{$b}/recursos"],
            [function () use ($a) { $a->createResource(['nombre' => 'Sala']); }, 'POST', "{$b}/recursos"],
            [function () use ($a) { $a->updateResource('r1', ['cantidad' => 2]); }, 'PATCH', "{$b}/recursos/r1"],
            [function () use ($a) { $a->deleteResource('r1'); }, 'DELETE', "{$b}/recursos/r1"],
            [function () use ($a) { $a->listPackages(['contactId' => 'c1']); }, 'GET', "{$b}/paquetes"],
            [function () use ($a) { $a->createPackage(['contactId' => 'c1', 'nombre' => 'x', 'sesiones' => 5]); }, 'POST', "{$b}/paquetes"],
            [function () use ($a) { $a->voidPackage('p1'); }, 'DELETE', "{$b}/paquetes/p1"],
            [function () use ($a) { $a->report(['desde' => '2026-10-01']); }, 'GET', "{$b}/reporte"],
            [function () use ($a) { $a->listWaitlist(); }, 'GET', "{$b}/lista-de-espera"],
            [function () use ($a) { $a->addToWaitlist(['contactId' => 'c1', 'typeId' => 't1', 'desde' => 'a', 'hasta' => 'b']); }, 'POST', "{$b}/lista-de-espera"],
            [function () use ($a) { $a->removeFromWaitlist('w1'); }, 'DELETE', "{$b}/lista-de-espera/w1"],
            [function () use ($a) { $a->listStaff(); }, 'GET', "{$b}/staff"],
            [function () use ($a) { $a->getAvailability(); }, 'GET', "{$b}/availability"],
            [function () use ($a) { $a->listExceptions(); }, 'GET', "{$b}/exceptions"],
            [function () use ($a) { $a->removeException('e1'); }, 'DELETE', "{$b}/exceptions/e1"],
            [function () use ($a) { $a->getSlots(['typeId' => 't1']); }, 'GET', "{$b}/slots"],
            [function () use ($a) { $a->listAppointments(['status' => 'CONFIRMED']); }, 'GET', "{$b}/appointments"],
            [function () use ($a) { $a->book(['typeId' => 't1', 'contactId' => 'c1', 'startAt' => 'x']); }, 'POST', "{$b}/appointments"],
            [function () use ($a) { $a->updateAppointment('ap1', ['notes' => 'n']); }, 'PATCH', "{$b}/appointments/ap1"],
            [function () use ($a) { $a->cancel('ap1', ['reason' => 'r']); }, 'PATCH', "{$b}/appointments/ap1/cancel"],
            [function () use ($a) { $a->reschedule('ap1', ['startAt' => 'x']); }, 'PATCH', "{$b}/appointments/ap1/reschedule"],
            [function () use ($a) { $a->setStatus('ap1', ['status' => 'COMPLETED']); }, 'PATCH', "{$b}/appointments/ap1/status"],
            [function () use ($a) { $a->requestEmail('ap1'); }, 'POST', "{$b}/appointments/ap1/pedir-correo"],
            [function () use ($a) { $a->counts(); }, 'GET', "{$b}/counts"],
            [function () use ($a) { $a->googleStatus(); }, 'GET', "{$b}/google/status"],
            [function () use ($a) { $a->googleConnect(); }, 'GET', "{$b}/google/connect"],
            [function () use ($a) { $a->updateGoogle(['pushEnabled' => true]); }, 'PATCH', "{$b}/google"],
            [function () use ($a) { $a->disconnectGoogle(); }, 'DELETE', "{$b}/google"],
            [function () use ($a) { $a->sendBookingFlow(['contactId' => 'c1', 'appointmentTypeId' => 't1']); }, 'POST', "{$b}/booking-flow/send"],
            [function () use ($a) { $a->googleCallback(['code' => 'c', 'state' => 's']); }, 'GET', '/agenda/google/callback'],
            [function () use ($a) { $a->bookingFlowData(['encrypted_flow_data' => 'a', 'encrypted_aes_key' => 'b', 'initial_vector' => 'c']); }, 'POST', '/agenda/flow/data'],
            [function () use ($a) { $a->icsFeed('tok'); }, 'GET', '/public/agenda/tok/calendar.ics'],
        ]);
    }

    public function testAgendaStaffUserIdGoesInQuery(): void
    {
        $r = new RecordingHttpClient();
        $a = new AgendaResource($r, self::ORG);

        $a->setAvailability(['staffUserId' => 'u1', 'rules' => [['weekday' => 1, 'startMinute' => 480, 'endMinute' => 1020]]]);
        self::assertSame('PUT', $r->lastCall()['method']);
        self::assertSame(['staffUserId' => 'u1'], $this->query($r));
        self::assertSame(['rules' => [['weekday' => 1, 'startMinute' => 480, 'endMinute' => 1020]]], $r->lastBody());

        $a->addException(['staffUserId' => 'u1', 'date' => '2026-12-25']);
        self::assertSame(['staffUserId' => 'u1'], $this->query($r));
        self::assertSame(['date' => '2026-12-25'], $r->lastBody());
    }

    public function testAgendaPublicRoutesReturnRawBody(): void
    {
        $r = new RecordingHttpClient();
        $r->nextData = 'x';
        $ics = (new AgendaResource($r, self::ORG))->icsFeed('tok');
        // El doble devuelve un envelope JSON; en `raw` llega tal cual, sin decodificar.
        self::assertSame('{"data":"x"}', $ics);
        self::assertTrue($r->lastCall()['opts']['raw']);
    }

    public function testSales(): void
    {
        $r = new RecordingHttpClient();
        $s = new SalesResource($r, self::ORG);
        $b = '/organizations/org-1/sales';
        $e = "{$b}/einvoicing";
        $this->assertCalls($r, [
            [function () use ($s) { $s->entitlement(); }, 'GET', "{$b}/entitlement"],
            [function () use ($s) { $s->setAddons(['einvoicing' => true]); }, 'PUT', "{$b}/addons"],
            [function () use ($s) { $s->listProducts(['q' => 'x']); }, 'GET', "{$b}/products"],
            [function () use ($s) { $s->getProduct('p1'); }, 'GET', "{$b}/products/p1"],
            [function () use ($s) { $s->createProduct(['name' => 'x', 'priceCents' => 100]); }, 'POST', "{$b}/products"],
            [function () use ($s) { $s->updateProduct('p1', ['active' => false]); }, 'PATCH', "{$b}/products/p1"],
            [function () use ($s) { $s->deleteProduct('p1'); }, 'DELETE', "{$b}/products/p1"],
            [function () use ($s) { $s->createOrder(['items' => [['description' => 'x', 'quantity' => 1, 'unitPriceCents' => 100]]]); }, 'POST', "{$b}/orders"],
            [function () use ($s) { $s->listOrders(['status' => 'PAID']); }, 'GET', "{$b}/orders"],
            [function () use ($s) { $s->getOrder('o1'); }, 'GET', "{$b}/orders/o1"],
            [function () use ($s) { $s->updateOrderStatus('o1', ['status' => 'PAID']); }, 'PATCH', "{$b}/orders/o1/status"],
            [function () use ($s) { $s->deleteOrder('o1'); }, 'DELETE', "{$b}/orders/o1"],
            [function () use ($s) { $s->listBillingProfiles(); }, 'GET', "{$b}/billing-profiles"],
            [function () use ($s) { $s->getBillingProfile('bp1'); }, 'GET', "{$b}/billing-profiles/bp1"],
            [function () use ($s) { $s->createBillingProfile(['legalName' => 'x', 'docNumber' => '1']); }, 'POST', "{$b}/billing-profiles"],
            [function () use ($s) { $s->updateBillingProfile('bp1', ['email' => 'a@b.co']); }, 'PATCH', "{$b}/billing-profiles/bp1"],
            [function () use ($s) { $s->deleteBillingProfile('bp1'); }, 'DELETE', "{$b}/billing-profiles/bp1"],
            [function () use ($s) { $s->getEinvoicingConfig(['configId' => 'cf1']); }, 'GET', "{$e}/config"],
            [function () use ($s) { $s->updateEinvoicingConfig(['name' => 'x']); }, 'PUT', "{$e}/config"],
            [function () use ($s) { $s->validateEinvoicing(); }, 'POST', "{$e}/validate"],
            [function () use ($s) { $s->listEinvoicingCompanies(); }, 'GET', "{$e}/companies"],
            [function () use ($s) { $s->suggestEinvoicingCompany(['orderId' => 'o1']); }, 'GET', "{$e}/companies/sugerida"],
            [function () use ($s) { $s->deleteEinvoicingCompany('cf1'); }, 'DELETE', "{$e}/companies/cf1"],
            [function () use ($s) { $s->importEinvoicingCustomers(); }, 'POST', "{$e}/customers/import"],
            [function () use ($s) { $s->lookupEinvoicingCustomer(['doc' => '900']); }, 'GET', "{$e}/customer-lookup"],
            [function () use ($s) { $s->listInvoices(['docType' => 'INVOICE']); }, 'GET', "{$e}/invoices"],
            [function () use ($s) { $s->generateInvoice(['orderId' => 'o1']); }, 'POST', "{$e}/invoices"],
            [function () use ($s) { $s->createCreditNote(['invoiceId' => 'i1']); }, 'POST', "{$e}/credit-notes"],
            [function () use ($s) { $s->invoiceLicenseOrder(['customerOrderId' => 'co1']); }, 'POST', "{$e}/license-orders/invoice"],
            [function () use ($s) { $s->createLicenseInvoiceDraft(['customerOrderId' => 'co1']); }, 'POST', "{$e}/license-orders/draft"],
            [function () use ($s) { $s->sendInvoice('i1'); }, 'POST', "{$e}/invoices/i1/send"],
            [function () use ($s) { $s->getInvoicePdf('i1'); }, 'GET', "{$e}/invoices/i1/pdf"],
            [function () use ($s) { $s->publicInvoicePdf('tok'); }, 'GET', '/sales/invoice-pdf/tok'],
        ]);
    }

    public function testQuotes(): void
    {
        $r = new RecordingHttpClient();
        $q = new QuotesResource($r, self::ORG);
        $b = '/organizations/org-1/cotizaciones';
        $this->assertCalls($r, [
            [function () use ($q) { $q->availability(); }, 'GET', "{$b}/disponibilidad"],
            [function () use ($q) { $q->companies(['conversationId' => 'cv1']); }, 'GET', "{$b}/empresas"],
            [function () use ($q) { $q->uploadLogo(new \CURLFile(__FILE__)); }, 'POST', "{$b}/emisor/logo"],
            [function () use ($q) { $q->catalog(); }, 'GET', "{$b}/catalogo"],
            [function () use ($q) { $q->list(['estado' => 'ENVIADA']); }, 'GET', $b],
            [function () use ($q) { $q->create(['lineas' => []]); }, 'POST', $b],
            [function () use ($q) { $q->get('q1'); }, 'GET', "{$b}/q1"],
            [function () use ($q) { $q->update('q1', ['lineas' => []]); }, 'PUT', "{$b}/q1"],
            [function () use ($q) { $q->delete('q1'); }, 'DELETE', "{$b}/q1"],
            [function () use ($q) { $q->pdf('q1'); }, 'GET', "{$b}/q1/pdf"],
            [function () use ($q) { $q->viewLink('q1'); }, 'GET', "{$b}/q1/enlace"],
            [function () use ($q) { $q->send('q1', ['mensaje' => 'hola']); }, 'POST', "{$b}/q1/enviar"],
            [function () use ($q) { $q->duplicate('q1'); }, 'POST', "{$b}/q1/duplicar"],
            [function () use ($q) { $q->setStatus('q1', ['estado' => 'ACEPTADA']); }, 'PATCH', "{$b}/q1/estado"],
            [function () use ($q) { $q->convert('q1'); }, 'POST', "{$b}/q1/convertir"],
            [function () use ($q) { $q->publicPdf('tok'); }, 'GET', '/cotizaciones/pdf/tok'],
            [function () use ($q) { $q->viewPdf('tok', ['descargar' => '1']); }, 'GET', '/cotizaciones/vista/tok'],
        ]);
        self::assertSame(['descargar' => '1'], $this->query($r));
    }

    public function testQuotesUploadLogoIsMultipart(): void
    {
        $r = new RecordingHttpClient();
        (new QuotesResource($r, self::ORG))->uploadLogo(new \CURLFile(__FILE__));
        self::assertTrue($r->lastCall()['multipart']);
        self::assertArrayHasKey('file', $r->lastCall()['opts']['multipart']);
    }

    public function testOrdersAndCatalogs(): void
    {
        $r = new RecordingHttpClient();
        $o = new OrdersResource($r, self::ORG);
        $c = new CatalogsResource($r, self::ORG);
        $this->assertCalls($r, [
            [function () use ($o) { $o->getByMessage('m1'); }, 'GET', '/organizations/org-1/orders/by-message/m1'],
            [function () use ($o) { $o->updateStatus('o1', ['status' => 'COMPLETED']); }, 'PATCH', '/organizations/org-1/orders/o1'],
            [function () use ($c) { $c->connect('w1'); }, 'POST', '/organizations/org-1/catalog/waba/w1/connect'],
            [function () use ($c) { $c->sync('w1'); }, 'POST', '/organizations/org-1/catalog/waba/w1/sync'],
            [function () use ($c) { $c->listProducts(['catalogId' => 'cat1']); }, 'GET', '/organizations/org-1/catalog/products'],
            [function () use ($c) { $c->getCommerceSettings(['phoneNumberId' => 'ph1']); }, 'GET', '/organizations/org-1/catalog/commerce-settings'],
            [function () use ($c) { $c->updateCommerceSettings(['phoneNumberId' => 'ph1', 'isCartEnabled' => true]); }, 'PATCH', '/organizations/org-1/catalog/commerce-settings'],
        ]);
        self::assertSame(['phoneNumberId' => 'ph1'], $this->query($r));
        self::assertSame(['isCartEnabled' => true], $r->lastBody());
    }

    public function testPayments(): void
    {
        $r = new RecordingHttpClient();
        $n = new NequiPaymentsResource($r, self::ORG);
        $p = new PaymentReportsResource($r, self::ORG);
        $w = new WompiResource($r, self::ORG);
        $mp = new MercadoPagoResource($r, self::ORG);
        $this->assertCalls($r, [
            [function () use ($n) { $n->enabled(); }, 'GET', '/organizations/org-1/billing/nequi/enabled'],
            [function () use ($n) { $n->createPush(['phoneNumber' => '3001234567', 'amount' => 10]); }, 'POST', '/organizations/org-1/billing/nequi/push'],
            [function () use ($n) { $n->createQr(['invoiceId' => 'i1']); }, 'POST', '/organizations/org-1/billing/nequi/qr'],
            [function () use ($n) { $n->status('np1'); }, 'GET', '/organizations/org-1/billing/nequi/np1/status'],
            [function () use ($n) { $n->cancel('np1'); }, 'POST', '/organizations/org-1/billing/nequi/np1/cancel'],
            [function () use ($p) { $p->brebInfo(); }, 'GET', '/organizations/org-1/billing/payment-reports/breb-info'],
            [function () use ($p) { $p->create(new \CURLFile(__FILE__), ['amount' => 50000, 'invoiceId' => 'i1']); }, 'POST', '/organizations/org-1/billing/payment-reports'],
            [function () use ($p) { $p->list(); }, 'GET', '/organizations/org-1/billing/payment-reports'],
            [function () use ($p) { $p->receiptUrl('pr1'); }, 'GET', '/organizations/org-1/billing/payment-reports/pr1/receipt'],
            [function () use ($w) { $w->webhook(['event' => 'transaction.updated', 'data' => []]); }, 'POST', '/webhooks/wompi'],
            [function () use ($w) { $w->webhookForOrg(['event' => 'transaction.updated', 'data' => []]); }, 'POST', '/webhooks/wompi/org-1'],
            [function () use ($mp) { $mp->webhook(['type' => 'payment', 'data' => ['id' => '1']], ['headers' => ['x-signature' => 's']]); }, 'POST', '/webhooks/mercado-pago'],
        ]);
        self::assertSame(['x-signature' => 's'], $r->lastCall()['opts']['headers']);
    }

    public function testPaymentReportCreateSendsReceiptAndFieldsAsMultipart(): void
    {
        $r = new RecordingHttpClient();
        (new PaymentReportsResource($r, self::ORG))->create(new \CURLFile(__FILE__), ['amount' => 50000, 'note' => 'ref']);
        $mp = $r->lastCall()['opts']['multipart'];
        self::assertTrue($r->lastCall()['multipart']);
        self::assertInstanceOf(\CURLFile::class, $mp['receipt']);
        self::assertSame('50000', $mp['amount']);
        self::assertSame('ref', $mp['note']);
    }
}
