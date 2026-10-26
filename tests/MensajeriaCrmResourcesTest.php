<?php

declare(strict_types=1);

namespace Mosend\Tests;

use Mosend\Resources\BroadcastsResource;
use Mosend\Resources\ContactListsResource;
use Mosend\Resources\ContactsResource;
use Mosend\Resources\ConversationsResource;
use Mosend\Resources\CrmResource;
use Mosend\Resources\DealsResource;
use Mosend\Resources\DocumentsResource;
use Mosend\Resources\FlowsResource;
use Mosend\Resources\LinkPagesResource;
use Mosend\Resources\MessagesResource;
use Mosend\Resources\PhoneNumbersResource;
use Mosend\Resources\SignupLinksResource;
use Mosend\Resources\TagsResource;
use Mosend\Resources\TemplatesResource;
use Mosend\Resources\WabaResource;
use Mosend\Resources\WebChatPublicResource;
use Mosend\Resources\WebChatResource;
use Mosend\Resources\WhatsappFlowsResource;
use Mosend\Resources\WhatsappGroupsResource;
use Mosend\Tests\Support\RecordingHttpClient;
use PHPUnit\Framework\TestCase;

/** Mensajería y CRM: método + ruta (y cuerpo donde importa) de cada endpoint agregado. */
final class MensajeriaCrmResourcesTest extends TestCase
{
    private const ORG = 'org-1';

    /** @var RecordingHttpClient */
    private $http;

    protected function setUp(): void
    {
        $this->http = new RecordingHttpClient();
    }

    private function assertCall(string $method, string $path): void
    {
        $call = $this->http->lastCall();
        self::assertSame($method, $call['method']);
        self::assertSame($path, parse_url($call['url'], PHP_URL_PATH));
    }

    /** @return array<string,string> */
    private function query(): array
    {
        $q = parse_url($this->http->lastCall()['url'], PHP_URL_QUERY);
        $out = [];
        parse_str(is_string($q) ? $q : '', $out);
        /** @var array<string,string> $out */
        return $out;
    }

    private function o(string $path): string
    {
        return '/organizations/' . self::ORG . $path;
    }

    public function testConversations(): void
    {
        $r = new ConversationsResource($this->http, self::ORG);

        $r->getMessagingSettings();
        $this->assertCall('GET', $this->o('/conversations/messaging-settings'));
        $r->updateMessagingSettings(['markReadOn' => 'ATENDER']);
        $this->assertCall('PATCH', $this->o('/conversations/messaging-settings'));
        self::assertSame(['markReadOn' => 'ATENDER'], $this->http->lastBody());

        $r->getNotificationSettings();
        $this->assertCall('GET', $this->o('/conversations/notification-settings'));
        $r->updateNotificationSettings(['conversationDelayEmail' => ['enabled' => true]]);
        $this->assertCall('PATCH', $this->o('/conversations/notification-settings'));
        self::assertSame(['conversationDelayEmail' => ['enabled' => true]], $this->http->lastBody());

        $r->getWorkloadSettings();
        $this->assertCall('GET', $this->o('/conversations/workload/settings'));
        $r->updateWorkloadSettings(['slaResponseThresholdMin' => 5]);
        $this->assertCall('PATCH', $this->o('/conversations/workload/settings'));

        $r->unreadCounts(['phoneNumberId' => 'p-1']);
        $this->assertCall('GET', $this->o('/conversations/unread-counts'));
        self::assertSame('p-1', $this->query()['phoneNumberId']);

        $r->search(['q' => 'factura', 'conversationId' => 'c-1']);
        $this->assertCall('GET', $this->o('/conversations/search'));
        self::assertSame('factura', $this->query()['q']);

        $r->workload();
        $this->assertCall('GET', $this->o('/conversations/workload'));
        $r->workloadByAgent('u-1', ['days' => 7]);
        $this->assertCall('GET', $this->o('/conversations/workload/agent/u-1'));
        $r->activityTrends(['desde' => '2026-10-01']);
        $this->assertCall('GET', $this->o('/conversations/activity-trends'));
        $r->activityHeatmap(['metric' => 'late']);
        $this->assertCall('GET', $this->o('/conversations/activity-heatmap'));

        $r->bulkAssign(['conversationIds' => ['c-1'], 'userId' => 'u-1']);
        $this->assertCall('POST', $this->o('/conversations/bulk-assign'));
        self::assertSame(['conversationIds' => ['c-1'], 'userId' => 'u-1'], $this->http->lastBody());

        $r->listMessages('c-1', ['around' => 'm-1']);
        $this->assertCall('GET', $this->o('/conversations/c-1/messages'));
        self::assertSame('m-1', $this->query()['around']);

        $r->setStatus('c-1', ['status' => 'closed', 'category' => 'SALE']);
        $this->assertCall('PATCH', $this->o('/conversations/c-1/status'));
        self::assertSame(['status' => 'closed', 'category' => 'SALE'], $this->http->lastBody());
        $r->categorize('c-1', ['resolutionOutcome' => 'RESOLVED']);
        $this->assertCall('PATCH', $this->o('/conversations/c-1/categorize'));

        $r->markRead('c-1');
        $this->assertCall('POST', $this->o('/conversations/c-1/read'));
        $r->markUnread('c-1');
        $this->assertCall('POST', $this->o('/conversations/c-1/unread'));
        $r->sendTyping('c-1');
        $this->assertCall('POST', $this->o('/conversations/c-1/typing'));

        $r->assign('c-1', ['userId' => null]);
        $this->assertCall('PATCH', $this->o('/conversations/c-1/assign'));
        self::assertSame(['userId' => null], $this->http->lastBody());
        $r->claim('c-1');
        $this->assertCall('POST', $this->o('/conversations/c-1/claim'));

        $r->requestHandoff('c-1', ['detail' => 'pidió ventas']);
        $this->assertCall('POST', $this->o('/conversations/c-1/request-handoff'));
        self::assertSame(['detail' => 'pidió ventas'], $this->http->lastBody());
        $r->requestHandoff('c-1');
        self::assertNull($this->http->lastBody());

        $r->snooze('c-1', ['until' => '2026-11-01T09:00:00Z', 'note' => 'llamar']);
        $this->assertCall('PATCH', $this->o('/conversations/c-1/snooze'));
        $r->pin('c-1', true);
        $this->assertCall('PATCH', $this->o('/conversations/c-1/pin'));
        self::assertSame(['pinned' => true], $this->http->lastBody());

        $r->addTag('c-1', 't-1');
        $this->assertCall('POST', $this->o('/conversations/c-1/tags'));
        self::assertSame(['tagId' => 't-1'], $this->http->lastBody());
        $r->removeTag('c-1', 't-1');
        $this->assertCall('DELETE', $this->o('/conversations/c-1/tags/t-1'));

        $r->listMedia('c-1', ['category' => 'image']);
        $this->assertCall('GET', $this->o('/conversations/c-1/media'));
        $r->mediaCounts('c-1');
        $this->assertCall('GET', $this->o('/conversations/c-1/media/counts'));
        $r->listLinks('c-1');
        $this->assertCall('GET', $this->o('/conversations/c-1/links'));

        $r->setAgent('c-1', null);
        $this->assertCall('PATCH', $this->o('/conversations/c-1/agent'));
        self::assertSame(['agentId' => null], $this->http->lastBody());
        $r->returnToBot('c-1');
        $this->assertCall('POST', $this->o('/conversations/c-1/devolver-al-bot'));
        $r->takeThread('c-1');
        $this->assertCall('POST', $this->o('/conversations/c-1/tomar-hilo'));
    }

    public function testMessages(): void
    {
        $r = new MessagesResource($this->http, self::ORG);

        $r->restore('m-1');
        $this->assertCall('POST', $this->o('/messages/m-1/restore'));
        $r->getMedia('m-1');
        $this->assertCall('GET', $this->o('/messages/m-1/media'));
        $r->upload(new \CURLFile(__FILE__), 'p-1');
        $this->assertCall('POST', $this->o('/messages/upload'));
        self::assertTrue($this->http->lastCall()['multipart']);
        self::assertSame('p-1', $this->query()['phoneNumberId']);

        $r->sendInteractive(['phoneNumberId' => 'p', 'to' => '57300', 'kind' => 'list', 'buttonText' => 'Ver'], ['idempotencyKey' => 'k-1']);
        $this->assertCall('POST', $this->o('/messages/interactive'));
        self::assertSame('list', $this->http->lastBody()['kind']);
        self::assertSame('k-1', $this->http->lastCall()['opts']['idempotencyKey']);
        $r->sendProduct(['phoneNumberId' => 'p', 'to' => '57300', 'catalogId' => 'cat']);
        $this->assertCall('POST', $this->o('/messages/product'));
        $r->sendFlow(['phoneNumberId' => 'p', 'to' => '57300', 'flowId' => 'f', 'bodyText' => 'b', 'ctaText' => 'c']);
        $this->assertCall('POST', $this->o('/messages/flow'));
        $r->sendCarousel(['phoneNumberId' => 'p', 'to' => '57300', 'bodyText' => 'b', 'cards' => [], 'orgId' => 'org-2']);
        $this->assertCall('POST', '/organizations/org-2/messages/carousel');
        self::assertArrayNotHasKey('orgId', $this->http->lastBody());
        $r->reveal('m-1');
        $this->assertCall('POST', $this->o('/messages/m-1/revelar'));
    }

    public function testContacts(): void
    {
        $r = new ContactsResource($this->http, self::ORG);

        $r->list(['etapa' => 'CLIENTE', 'duenoUserId' => 'ninguno', 'campos' => ['segmento' => 'VIP'], 'compraEnDias' => 30]);
        $this->assertCall('GET', $this->o('/contacts'));
        $q = $this->query();
        self::assertSame('CLIENTE', $q['etapa']);
        self::assertSame('ninguno', $q['duenoUserId']);
        self::assertSame('{"segmento":"VIP"}', $q['campos']);
        self::assertSame('30', $q['compraEnDias']);

        $r->export(['sinCompraDias' => 90, 'gastoMin' => 1000, 'puntajeMin' => 10, 'empresaId' => 'e-1']);
        $this->assertCall('GET', $this->o('/contacts/export'));
        self::assertSame('90', $this->query()['sinCompraDias']);
        self::assertSame('e-1', $this->query()['empresaId']);

        $r->import(new \CURLFile(__FILE__), ['defaultCountry' => 'CO', 'defaultOptIn' => true, 'mapeo' => ['Teléfono' => 'phone']]);
        $this->assertCall('POST', $this->o('/contacts/import'));
        $opts = $this->http->lastCall()['opts'];
        self::assertSame(['defaultCountry' => 'CO', 'defaultOptIn' => 'true'], $opts['query']);
        self::assertSame(['Teléfono' => 'phone'], json_decode($opts['multipart']['mapeo'], true));

        $r->previewImport(new \CURLFile(__FILE__));
        $this->assertCall('POST', $this->o('/contacts/import/preview'));
        self::assertTrue($this->http->lastCall()['multipart']);

        $r->uploadNoteAttachment('ct-1', new \CURLFile(__FILE__));
        $this->assertCall('POST', $this->o('/contacts/ct-1/notes/adjuntos'));
        self::assertTrue($this->http->lastCall()['multipart']);

        $adjunto = ['mediaId' => 'md', 'nombre' => 'f.pdf', 'tipo' => 'application/pdf', 'tamano' => 10];
        $r->addNote('ct-1', ['body' => 'Hola', 'menciones' => ['u-1'], 'adjuntos' => [$adjunto]]);
        $this->assertCall('POST', $this->o('/contacts/ct-1/notes'));
        self::assertSame(['body' => 'Hola', 'menciones' => ['u-1'], 'adjuntos' => [$adjunto]], $this->http->lastBody());
    }

    public function testContactLists(): void
    {
        $r = new ContactListsResource($this->http, self::ORG);

        $r->removeMembers('l-1', ['contactIds' => ['a']]);
        $this->assertCall('DELETE', $this->o('/contact-lists/l-1/members'));
        self::assertSame(['contactIds' => ['a']], $this->http->lastBody());
        $r->addByFilter('l-1', ['tagId' => 't-1']);
        $this->assertCall('POST', $this->o('/contact-lists/l-1/add-by-filter'));
        $r->removeByFilter('l-1', ['optInStatus' => 'OPTED_OUT']);
        $this->assertCall('POST', $this->o('/contact-lists/l-1/remove-by-filter'));
        $r->clear('l-1');
        $this->assertCall('POST', $this->o('/contact-lists/l-1/clear'));
        $r->import('l-1', new \CURLFile(__FILE__), ['mapeo' => ['Nombre' => 'name'], 'defaultOptIn' => false]);
        $this->assertCall('POST', $this->o('/contact-lists/l-1/import'));
        self::assertSame(['defaultOptIn' => 'false'], $this->http->lastCall()['opts']['query']);

        $r->create(['name' => 'Dormidos', 'smartFilter' => ['sinCompraDias' => 90]]);
        self::assertSame(['name' => 'Dormidos', 'smartFilter' => ['sinCompraDias' => 90]], $this->http->lastBody());
        $r->listMembers('l-1', ['pageSize' => 100]);
        self::assertSame('100', $this->query()['pageSize']);
        $r->listMembers('l-1', 'org-2');
        $this->assertCall('GET', '/organizations/org-2/contact-lists/l-1/members');
    }

    public function testTagsTemplatesBroadcastsFlowsWabaDocuments(): void
    {
        (new TagsResource($this->http, self::ORG))->update('t-1', ['color' => '#ff0000']);
        $this->assertCall('PATCH', $this->o('/tags/t-1'));
        self::assertSame(['color' => '#ff0000'], $this->http->lastBody());

        $tpl = new TemplatesResource($this->http, self::ORG);
        $tpl->copy('tp-1', 'w-2');
        $this->assertCall('POST', $this->o('/templates/tp-1/copiar'));
        self::assertSame(['wabaId' => 'w-2'], $this->http->lastBody());
        $tpl->setHeaderMedia('tp-1', new \CURLFile(__FILE__));
        $this->assertCall('POST', $this->o('/templates/tp-1/header-media'));
        self::assertTrue($this->http->lastCall()['multipart']);
        $tpl->clearHeaderMedia('tp-1');
        $this->assertCall('DELETE', $this->o('/templates/tp-1/header-media'));

        (new BroadcastsResource($this->http, self::ORG))->retryFailed('b-1');
        $this->assertCall('POST', $this->o('/broadcasts/b-1/retry-failed'));

        (new FlowsResource($this->http, self::ORG))->templates();
        $this->assertCall('GET', $this->o('/bot/flows/plantillas'));

        (new WabaResource($this->http, self::ORG))->resubscribeWebhooks('w-1');
        $this->assertCall('POST', $this->o('/waba/w-1/webhooks/resubscribe'));

        $docs = new DocumentsResource($this->http, self::ORG);
        $docs->editorStatus();
        $this->assertCall('GET', $this->o('/documents/editor/status'));
        $docs->editorConfig('d-1', 'view');
        $this->assertCall('GET', $this->o('/documents/d-1/editor-config'));
        self::assertSame('view', $this->query()['mode']);
    }

    public function testPhoneNumbers(): void
    {
        $r = new PhoneNumbersResource($this->http, self::ORG);

        $r->update('p-1', 'violet');
        $this->assertCall('PATCH', $this->o('/phone-numbers/p-1'));
        self::assertSame(['color' => 'violet'], $this->http->lastBody());
        $r->serviceUsage();
        $this->assertCall('GET', $this->o('/phone-numbers/service-usage'));
        $r->connectionHistory('p-1');
        $this->assertCall('GET', $this->o('/phone-numbers/p-1/historial-de-conexion'));
        $r->archiveImpact('p-1');
        $this->assertCall('GET', $this->o('/phone-numbers/p-1/impacto-al-archivar'));
        $r->syncCoexistence('p-1');
        $this->assertCall('POST', $this->o('/phone-numbers/p-1/coexistence/sync'));
        $r->reconnect('p-1');
        $this->assertCall('POST', $this->o('/phone-numbers/p-1/reconnect'));
        $r->setUsername('p-1', ['username' => 'mi.tienda', 'transferAction' => 'force_transfer']);
        $this->assertCall('POST', $this->o('/phone-numbers/p-1/username'));
        self::assertSame(['username' => 'mi.tienda', 'transferAction' => 'force_transfer'], $this->http->lastBody());
    }

    public function testWebChatAndLinkPages(): void
    {
        $wc = new WebChatResource($this->http, self::ORG);
        $wc->uploadAvatar('ch-1', new \CURLFile(__FILE__));
        $this->assertCall('POST', $this->o('/web-chat/channels/ch-1/avatar'));
        $wc->deleteAvatar('ch-1');
        $this->assertCall('DELETE', $this->o('/web-chat/channels/ch-1/avatar'));

        $pub = new WebChatPublicResource($this->http);
        $pub->getVapidKey('tok');
        $this->assertCall('GET', '/web-chat/tok/push/vapid');
        self::assertTrue($this->http->lastCall()['opts']['skipAuth']);

        $sub = ['endpoint' => 'https://push/x', 'keys' => ['p256dh' => 'k', 'auth' => 'a']];
        $pub->pushSubscribe('tok', 'vjwt', $sub);
        $this->assertCall('POST', '/web-chat/tok/push/subscribe');
        self::assertSame(['subscription' => $sub], $this->http->lastBody());
        self::assertSame(['Authorization' => 'Bearer vjwt'], $this->http->lastCall()['opts']['headers']);
        $pub->pushUnsubscribe('tok', 'vjwt', 'https://push/x');
        $this->assertCall('POST', '/web-chat/tok/push/unsubscribe');
        self::assertSame(['endpoint' => 'https://push/x'], $this->http->lastBody());
        $pub->presence('tok', 'vjwt', true);
        $this->assertCall('POST', '/web-chat/tok/presence');
        self::assertSame(['jwt' => 'vjwt', 'away' => '1'], $this->http->lastBody());
        $pub->reportClientError('tok', ['message' => 'boom']);
        $this->assertCall('POST', '/web-chat/tok/client-error');

        $lp = new LinkPagesResource($this->http, self::ORG);
        $lp->publicProfile('mi-tienda');
        $this->assertCall('GET', '/link-pages/p/mi-tienda');
        self::assertSame('/link-pages/p/mi-tienda/avatar', $lp->publicImagePath('mi-tienda', 'avatar'));
        self::assertSame('/link-pages/p/mi-tienda/cover', $lp->publicImagePath('mi-tienda', 'cover'));
        self::assertSame('/link-pages/p/mi-tienda/go/it-1', $lp->publicItemPath('mi-tienda', 'it-1'));
    }

    public function testWhatsappFlows(): void
    {
        $r = new WhatsappFlowsResource($this->http, self::ORG);

        $r->list(['wabaId' => 'w-1']);
        $this->assertCall('GET', $this->o('/whatsapp-flows'));
        self::assertSame('w-1', $this->query()['wabaId']);
        $r->create(['wabaId' => 'w-1', 'name' => 'Registro', 'categories' => ['SIGN_UP']]);
        $this->assertCall('POST', $this->o('/whatsapp-flows'));
        self::assertSame(['wabaId' => 'w-1', 'name' => 'Registro', 'categories' => ['SIGN_UP']], $this->http->lastBody());
        $r->sync('w-1');
        $this->assertCall('POST', $this->o('/whatsapp-flows/sync/w-1'));
        $r->get('f-1');
        $this->assertCall('GET', $this->o('/whatsapp-flows/f-1'));
        $r->updateJson('f-1', '{}');
        $this->assertCall('PATCH', $this->o('/whatsapp-flows/f-1/json'));
        self::assertSame(['flowJson' => '{}'], $this->http->lastBody());
        $r->update('f-1', ['name' => 'Nuevo']);
        $this->assertCall('PATCH', $this->o('/whatsapp-flows/f-1'));
        $r->publish('f-1');
        $this->assertCall('POST', $this->o('/whatsapp-flows/f-1/publish'));
        $r->deprecate('f-1');
        $this->assertCall('POST', $this->o('/whatsapp-flows/f-1/deprecate'));
        $r->delete('f-1');
        $this->assertCall('DELETE', $this->o('/whatsapp-flows/f-1'));
    }

    public function testWhatsappGroups(): void
    {
        $r = new WhatsappGroupsResource($this->http, self::ORG);

        $r->list(['phoneNumberId' => 'p-1']);
        $this->assertCall('GET', $this->o('/whatsapp-groups'));
        $r->create(['phoneNumberId' => 'p-1', 'subject' => 'VIP']);
        $this->assertCall('POST', $this->o('/whatsapp-groups'));
        self::assertSame(['phoneNumberId' => 'p-1', 'subject' => 'VIP'], $this->http->lastBody());
        $r->get('g-1');
        $this->assertCall('GET', $this->o('/whatsapp-groups/g-1'));
        $r->delete('g-1');
        $this->assertCall('DELETE', $this->o('/whatsapp-groups/g-1'));
        $r->getInviteLink('g-1');
        $this->assertCall('GET', $this->o('/whatsapp-groups/g-1/invite-link'));
        $r->resetInviteLink('g-1');
        $this->assertCall('POST', $this->o('/whatsapp-groups/g-1/invite-link'));
        $r->removeParticipants('g-1', ['573000000000']);
        $this->assertCall('DELETE', $this->o('/whatsapp-groups/g-1/participants'));
        self::assertSame(['waIds' => ['573000000000']], $this->http->lastBody());
        $r->listJoinRequests('g-1');
        $this->assertCall('GET', $this->o('/whatsapp-groups/g-1/join-requests'));
        $r->resolveJoinRequests('g-1', ['jr-1'], 'aprobar');
        $this->assertCall('POST', $this->o('/whatsapp-groups/g-1/join-requests'));
        self::assertSame(['joinRequestIds' => ['jr-1'], 'accion' => 'aprobar'], $this->http->lastBody());
    }

    public function testSignupLinks(): void
    {
        $r = new SignupLinksResource($this->http, self::ORG);

        $r->list(['wabaId' => 'w-1']);
        $this->assertCall('GET', $this->o('/signup-links'));
        self::assertSame('w-1', $this->query()['wabaId']);
        $r->create(['wabaId' => 'w-1', 'signupMessage' => 's', 'confirmationMessage' => 'c', 'privacyPolicyUrl' => 'https://x.co/p']);
        $this->assertCall('POST', $this->o('/signup-links'));
        $r->retrieve('sl-1');
        $this->assertCall('GET', $this->o('/signup-links/sl-1'));
        $r->update('sl-1', ['status' => 'DISABLED']);
        $this->assertCall('PATCH', $this->o('/signup-links/sl-1'));
        self::assertSame(['status' => 'DISABLED'], $this->http->lastBody());
    }

    public function testCrm(): void
    {
        $r = new CrmResource($this->http, self::ORG);

        $r->getSettings();
        $this->assertCall('GET', $this->o('/crm/configuracion'));
        $r->updateSettings(['diasParaInactivo' => 90, 'asignacionDeDueno' => 'ROTATIVO']);
        $this->assertCall('PUT', $this->o('/crm/configuracion'));
        self::assertSame(['diasParaInactivo' => 90, 'asignacionDeDueno' => 'ROTATIVO'], $this->http->lastBody());

        $r->listFields();
        $this->assertCall('GET', $this->o('/crm/campos'));
        $r->suggestedFields();
        $this->assertCall('GET', $this->o('/crm/campos/sugeridos'));
        $r->createField(['etiqueta' => 'Segmento', 'tipo' => 'LISTA', 'opciones' => ['VIP']]);
        $this->assertCall('POST', $this->o('/crm/campos'));
        $r->updateField('cp-1', ['obligatorio' => true]);
        $this->assertCall('PATCH', $this->o('/crm/campos/cp-1'));
        $r->archiveField('cp-1');
        $this->assertCall('DELETE', $this->o('/crm/campos/cp-1'));
        $r->mergeFieldKeys(['de' => ['Numero'], 'a' => 'telefono']);
        $this->assertCall('POST', $this->o('/crm/campos/unificar'));
        self::assertSame(['de' => ['Numero'], 'a' => 'telefono'], $this->http->lastBody());

        $r->updateClient('ct-1', ['etapa' => 'CLIENTE', 'campos' => ['segmento' => 'VIP']]);
        $this->assertCall('PATCH', $this->o('/crm/clientes/ct-1'));
        $r->bulkUpdateClients(['contactIds' => ['ct-1'], 'duenoUserId' => 'u-1']);
        $this->assertCall('POST', $this->o('/crm/clientes/masivo'));
        $r->getProfile('ct-1');
        $this->assertCall('GET', $this->o('/crm/clientes/ct-1/ficha'));
        $r->getTimeline('ct-1', ['limite' => 20]);
        $this->assertCall('GET', $this->o('/crm/clientes/ct-1/linea-de-tiempo'));
        self::assertSame('20', $this->query()['limite']);

        $r->listCompanies(['q' => 'acme']);
        $this->assertCall('GET', $this->o('/crm/empresas'));
        $r->getCompany('e-1');
        $this->assertCall('GET', $this->o('/crm/empresas/e-1'));
        $r->createCompany(['nombre' => 'Acme', 'nit' => '900123456']);
        $this->assertCall('POST', $this->o('/crm/empresas'));
        $r->updateCompany('e-1', ['ciudad' => 'Bogotá']);
        $this->assertCall('PATCH', $this->o('/crm/empresas/e-1'));
        $r->deleteCompany('e-1');
        $this->assertCall('DELETE', $this->o('/crm/empresas/e-1'));
        $r->lookupNit('900123456');
        $this->assertCall('POST', $this->o('/crm/empresas/consultar-nit'));
        self::assertSame(['nit' => '900123456'], $this->http->lastBody());
        $r->addCompanyContacts('e-1', ['ct-1']);
        $this->assertCall('POST', $this->o('/crm/empresas/e-1/contactos'));
        self::assertSame(['contactIds' => ['ct-1']], $this->http->lastBody());
        $r->removeCompanyContacts('e-1', ['ct-1']);
        $this->assertCall('POST', $this->o('/crm/empresas/e-1/contactos/quitar'));

        $r->listDuplicates();
        $this->assertCall('GET', $this->o('/crm/duplicados'));
        $r->mergeContacts('ct-1', 'ct-2');
        $this->assertCall('POST', $this->o('/crm/duplicados/fusionar'));
        self::assertSame(['principalId' => 'ct-1', 'duplicadoId' => 'ct-2'], $this->http->lastBody());
        $r->customersReport();
        $this->assertCall('GET', $this->o('/crm/reportes/clientes'));
    }

    public function testDeals(): void
    {
        $r = new DealsResource($this->http, self::ORG);

        $r->listPipelines();
        $this->assertCall('GET', $this->o('/crm/embudos'));
        $r->createPipeline(['nombre' => 'Ventas', 'conEtapasPorDefecto' => true]);
        $this->assertCall('POST', $this->o('/crm/embudos'));
        $r->updatePipeline('em-1', ['porDefecto' => true]);
        $this->assertCall('PATCH', $this->o('/crm/embudos/em-1'));
        $r->archivePipeline('em-1');
        $this->assertCall('DELETE', $this->o('/crm/embudos/em-1'));
        $r->createStage('em-1', ['nombre' => 'Cotizado', 'clave' => 'COTIZADO']);
        $this->assertCall('POST', $this->o('/crm/embudos/em-1/etapas'));
        $r->reorderStages('em-1', ['et-2', 'et-1']);
        $this->assertCall('PUT', $this->o('/crm/embudos/em-1/etapas/orden'));
        self::assertSame(['etapaIds' => ['et-2', 'et-1']], $this->http->lastBody());
        $r->updateStage('et-1', ['probabilidad' => 50]);
        $this->assertCall('PATCH', $this->o('/crm/etapas/et-1'));
        $r->deleteStage('et-1', 'et-2');
        $this->assertCall('DELETE', $this->o('/crm/etapas/et-1'));
        self::assertSame('et-2', $this->query()['moverA']);

        $r->board(['embudoId' => 'em-1']);
        $this->assertCall('GET', $this->o('/crm/oportunidades/tablero'));
        $r->list(['estado' => 'ABIERTA']);
        $this->assertCall('GET', $this->o('/crm/oportunidades'));
        $r->get('op-1');
        $this->assertCall('GET', $this->o('/crm/oportunidades/op-1'));
        $r->create(['titulo' => 'Licencias', 'monto' => 100000, 'moneda' => 'COP']);
        $this->assertCall('POST', $this->o('/crm/oportunidades'));
        $r->update('op-1', ['monto' => 200000]);
        $this->assertCall('PATCH', $this->o('/crm/oportunidades/op-1'));
        $r->move('op-1', ['etapaId' => 'et-2']);
        $this->assertCall('POST', $this->o('/crm/oportunidades/op-1/mover'));
        self::assertSame(['etapaId' => 'et-2'], $this->http->lastBody());
        $r->win('op-1', ['monto' => 150000]);
        $this->assertCall('POST', $this->o('/crm/oportunidades/op-1/ganar'));
        $r->win('op-1');
        self::assertNull($this->http->lastBody());
        $r->lose('op-1', 'precio');
        $this->assertCall('POST', $this->o('/crm/oportunidades/op-1/perder'));
        self::assertSame(['motivo' => 'precio'], $this->http->lastBody());
        $r->reopen('op-1');
        $this->assertCall('POST', $this->o('/crm/oportunidades/op-1/reabrir'));
        $r->delete('op-1');
        $this->assertCall('DELETE', $this->o('/crm/oportunidades/op-1'));

        $r->listSequences();
        $this->assertCall('GET', $this->o('/crm/cadencias'));
        $r->createSequence(['nombre' => 'Seguimiento', 'phoneNumberId' => 'p-1', 'pasos' => [['dias' => 1, 'plantilla' => 'hola']]]);
        $this->assertCall('POST', $this->o('/crm/cadencias'));
        $r->updateSequence('cd-1', ['activa' => false]);
        $this->assertCall('PATCH', $this->o('/crm/cadencias/cd-1'));
        $r->deleteSequence('cd-1');
        $this->assertCall('DELETE', $this->o('/crm/cadencias/cd-1'));
        $r->sequenceEnrollments('cd-1');
        $this->assertCall('GET', $this->o('/crm/cadencias/cd-1/inscritos'));
        $r->enroll('cd-1', ['contactIds' => ['ct-1']]);
        $this->assertCall('POST', $this->o('/crm/cadencias/cd-1/inscribir'));
        $r->stopEnrollment('in-1');
        $this->assertCall('POST', $this->o('/crm/inscripciones/in-1/detener'));

        $r->listAutomations();
        $this->assertCall('GET', $this->o('/crm/automatizaciones'));
        $r->createAutomation(['nombre' => 'Bienvenida', 'disparador' => 'contacto.creado', 'acciones' => []]);
        $this->assertCall('POST', $this->o('/crm/automatizaciones'));
        $r->updateAutomation('au-1', ['activa' => false]);
        $this->assertCall('PATCH', $this->o('/crm/automatizaciones/au-1'));
        $r->deleteAutomation('au-1');
        $this->assertCall('DELETE', $this->o('/crm/automatizaciones/au-1'));

        $r->report(['embudoId' => 'em-1', 'desde' => '2026-10-01']);
        $this->assertCall('GET', $this->o('/crm/reportes/embudo'));
        self::assertSame('2026-10-01', $this->query()['desde']);
    }
}
