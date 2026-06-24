<?php

declare(strict_types=1);

namespace Mosend\Tests;

use Mosend\Resources\AttendanceResource;
use Mosend\Resources\BroadcastsResource;
use Mosend\Resources\ContactListsResource;
use Mosend\Resources\ContactsResource;
use Mosend\Resources\DocumentsResource;
use Mosend\Resources\LinkPagesResource;
use Mosend\Resources\MessagesResource;
use Mosend\Resources\OptInsResource;
use Mosend\Resources\QuickRepliesResource;
use Mosend\Resources\ReactionsResource;
use Mosend\Resources\ScheduleResource;
use Mosend\Resources\ShiftRemindersResource;
use Mosend\Resources\SolutionsResource;
use Mosend\Resources\StoreConnectionsResource;
use Mosend\Resources\StoreTemplatesResource;
use Mosend\Resources\TagsResource;
use Mosend\Resources\TemplatesResource;
use Mosend\Tests\Support\RecordingHttpClient;
use PHPUnit\Framework\TestCase;

final class ResourcesTest extends TestCase
{
    private const ORG = 'org-1';

    private function rec(): RecordingHttpClient
    {
        return new RecordingHttpClient();
    }

    private function path(RecordingHttpClient $r): string
    {
        return parse_url($r->lastCall()['url'], PHP_URL_PATH) ?: '';
    }

    public function testMessagesSendPostsBodyAndIdempotency(): void
    {
        $r = $this->rec();
        (new MessagesResource($r, self::ORG))->send(
            ['phoneNumberId' => 'p1', 'to' => '573001234567', 'type' => 'text', 'payload' => ['body' => 'hi']],
            ['idempotencyKey' => 'k1']
        );
        $call = $r->lastCall();
        self::assertSame('POST', $call['method']);
        self::assertSame('/organizations/org-1/messages', $this->path($r));
        self::assertSame('573001234567', $r->lastBody()['to']);
        self::assertSame('k1', $call['opts']['idempotencyKey']);
        self::assertArrayNotHasKey('orgId', $r->lastBody());
    }

    public function testContactsListSendsQuery(): void
    {
        $r = $this->rec();
        (new ContactsResource($r, self::ORG))->list(['q' => 'juan', 'page' => 2]);
        self::assertSame('GET', $r->lastCall()['method']);
        self::assertSame('/organizations/org-1/contacts', $this->path($r));
        self::assertStringContainsString('q=juan', $r->lastCall()['url']);
        self::assertStringContainsString('page=2', $r->lastCall()['url']);
    }

    public function testContactsBulkDelete(): void
    {
        $r = $this->rec();
        (new ContactsResource($r, self::ORG))->bulkDelete(['contactIds' => ['c1', 'c2']]);
        self::assertSame('POST', $r->lastCall()['method']);
        self::assertSame('/organizations/org-1/contacts/bulk-delete', $this->path($r));
        self::assertSame(['c1', 'c2'], $r->lastBody()['contactIds']);
    }

    public function testContactsAddNoteNested(): void
    {
        $r = $this->rec();
        (new ContactsResource($r, self::ORG))->addNote('c9', ['body' => 'VIP', 'pinned' => true]);
        self::assertSame('/organizations/org-1/contacts/c9/notes', $this->path($r));
        self::assertTrue($r->lastBody()['pinned']);
    }

    public function testContactListsAddByTagAndRemoveMember(): void
    {
        $r = $this->rec();
        $res = new ContactListsResource($r, self::ORG);
        $res->addByTag('l1', ['tagIds' => ['t1']]);
        self::assertSame('/organizations/org-1/contact-lists/l1/add-by-tag', $this->path($r));
        self::assertSame(['t1'], $r->lastBody()['tagIds']);

        $res->removeMember('l1', 'c9');
        self::assertSame('DELETE', $r->lastCall()['method']);
        self::assertSame('/organizations/org-1/contact-lists/l1/members/c9', $this->path($r));
    }

    public function testTagsCreate(): void
    {
        $r = $this->rec();
        (new TagsResource($r, self::ORG))->create(['name' => 'VIP', 'color' => '#fff']);
        self::assertSame('/organizations/org-1/tags', $this->path($r));
        self::assertSame('VIP', $r->lastBody()['name']);
    }

    public function testOptInsCreateNestedUnderContact(): void
    {
        $r = $this->rec();
        (new OptInsResource($r, self::ORG))->create('c1', ['type' => 'IN', 'source' => 'web']);
        self::assertSame('/organizations/org-1/contacts/c1/opt-ins', $this->path($r));
        self::assertSame('IN', $r->lastBody()['type']);
    }

    public function testQuickRepliesCreate(): void
    {
        $r = $this->rec();
        (new QuickRepliesResource($r, self::ORG))->create(['shortcut' => '/hi', 'title' => 'Hola', 'body' => 'Hola!']);
        self::assertSame('/organizations/org-1/quick-replies', $this->path($r));
        self::assertSame('Hola', $r->lastBody()['title']);
    }

    public function testReactionsSetUsesPut(): void
    {
        $r = $this->rec();
        (new ReactionsResource($r, self::ORG))->set('m1', ['emoji' => '🎉']);
        self::assertSame('PUT', $r->lastCall()['method']);
        self::assertSame('/organizations/org-1/messages/m1/reactions', $this->path($r));
    }

    public function testTemplatesCreateAndUploadMultipart(): void
    {
        $r = $this->rec();
        $res = new TemplatesResource($r, self::ORG);
        $res->create(['wabaId' => 'w1', 'name' => 'promo', 'language' => 'es', 'category' => 'MARKETING', 'components' => []]);
        self::assertSame('/organizations/org-1/templates', $this->path($r));
        self::assertSame('MARKETING', $r->lastBody()['category']);

        $res->uploadHeaderMedia('/tmp/x.png');
        self::assertTrue($r->lastCall()['multipart']);
        self::assertSame('/organizations/org-1/templates/upload-header-media', $this->path($r));
    }

    public function testBroadcastsCreateSendRecipients(): void
    {
        $r = $this->rec();
        $res = new BroadcastsResource($r, self::ORG);
        $res->create(['name' => 'Q1', 'phoneNumberId' => 'p1', 'templateId' => 't1', 'templateLanguage' => 'es_CO']);
        self::assertSame('/organizations/org-1/broadcasts', $this->path($r));

        $res->send('b1');
        self::assertSame('POST', $r->lastCall()['method']);
        self::assertSame('/organizations/org-1/broadcasts/b1/send', $this->path($r));

        $res->recipients('b1', ['filter' => 'failed']);
        self::assertSame('/organizations/org-1/broadcasts/b1/recipients', $this->path($r));
        self::assertStringContainsString('filter=failed', $r->lastCall()['url']);
    }

    public function testDocumentsUploadMultipartWithFolderQuery(): void
    {
        $r = $this->rec();
        $res = new DocumentsResource($r, self::ORG);
        $res->upload('/tmp/x.pdf', ['folderId' => 'f1', 'name' => 'Factura', 'visibility' => 'ORG']);
        self::assertTrue($r->lastCall()['multipart']);
        self::assertSame('/organizations/org-1/documents', $this->path($r));
        self::assertStringContainsString('folderId=f1', $r->lastCall()['url']);
        self::assertSame('Factura', $r->lastCall()['opts']['multipart']['name']);

        $res->send('d1', ['conversationId' => 'c1', 'caption' => 'Hola']);
        self::assertSame('POST', $r->lastCall()['method']);
        self::assertSame('/organizations/org-1/documents/d1/send', $this->path($r));
        self::assertSame('c1', $r->lastBody()['conversationId']);

        $res->purge('d1');
        self::assertSame('DELETE', $r->lastCall()['method']);
        self::assertSame('/organizations/org-1/documents/d1/purge', $this->path($r));
    }

    public function testLinkPagesReorderAndItems(): void
    {
        $r = $this->rec();
        $res = new LinkPagesResource($r, self::ORG);
        $res->create(['handle' => 'mi_bio', 'displayName' => 'Mi Bio']);
        self::assertSame('/organizations/org-1/link-pages', $this->path($r));
        self::assertSame('mi_bio', $r->lastBody()['handle']);

        $res->reorderItems('lp1', ['i2', 'i1']);
        self::assertSame('PATCH', $r->lastCall()['method']);
        self::assertSame('/organizations/org-1/link-pages/lp1/items/reorder', $this->path($r));
        self::assertSame(['i2', 'i1'], $r->lastBody()['itemIds']);

        $res->archive('lp1');
        self::assertSame('DELETE', $r->lastCall()['method']);
        self::assertSame('/organizations/org-1/link-pages/lp1', $this->path($r));
    }

    public function testSolutionsCatalogAndInstall(): void
    {
        $r = $this->rec();
        $res = new SolutionsResource($r, self::ORG);
        $res->list();
        self::assertSame('/solutions', $this->path($r));

        $res->install('clinica', ['wabaId' => 'w1']);
        self::assertSame('POST', $r->lastCall()['method']);
        self::assertSame('/organizations/org-1/solutions/clinica/install', $this->path($r));
        self::assertSame('w1', $r->lastBody()['wabaId']);
    }

    public function testShiftRemindersUsesPut(): void
    {
        $r = $this->rec();
        (new ShiftRemindersResource($r, self::ORG))->update(['enabled' => true, 'shiftStartLeadMin' => 10]);
        self::assertSame('PUT', $r->lastCall()['method']);
        self::assertSame('/organizations/org-1/shift-reminders', $this->path($r));
        self::assertTrue($r->lastBody()['enabled']);
    }

    public function testStoreConnectionsMappingsAndEvents(): void
    {
        $r = $this->rec();
        $res = new StoreConnectionsResource($r, self::ORG);
        $res->upsertMapping('sc1', ['eventType' => 'ORDER_CREATED', 'templateId' => 't1']);
        self::assertSame('POST', $r->lastCall()['method']);
        self::assertSame('/organizations/org-1/store-connections/sc1/mappings', $this->path($r));
        self::assertSame('ORDER_CREATED', $r->lastBody()['eventType']);

        $res->listEvents('sc1', ['limit' => 25]);
        self::assertSame('GET', $r->lastCall()['method']);
        self::assertSame('/organizations/org-1/store-connections/sc1/events', $this->path($r));
        self::assertStringContainsString('limit=25', $r->lastCall()['url']);
    }

    public function testStoreTemplatesInstallAndStatus(): void
    {
        $r = $this->rec();
        $res = new StoreTemplatesResource($r, self::ORG);
        $res->status(['wabaId' => 'w1']);
        self::assertSame('/organizations/org-1/store-templates/status', $this->path($r));
        self::assertStringContainsString('wabaId=w1', $r->lastCall()['url']);

        $res->install(['catalogKey' => 'order_shipped', 'wabaId' => 'w1']);
        self::assertSame('POST', $r->lastCall()['method']);
        self::assertSame('/organizations/org-1/store-templates/install', $this->path($r));
        self::assertSame('order_shipped', $r->lastBody()['catalogKey']);
    }

    public function testAttendanceStartAndChangeStatus(): void
    {
        $r = $this->rec();
        $res = new AttendanceResource($r, self::ORG);
        $res->start();
        self::assertSame('POST', $r->lastCall()['method']);
        self::assertSame('/organizations/org-1/attendance/start', $this->path($r));

        $res->changeStatus(['status' => 'LUNCH']);
        self::assertSame('/organizations/org-1/attendance/status', $this->path($r));
        self::assertSame('LUNCH', $r->lastBody()['status']);

        $res->setOvertime('a1', ['enabled' => true]);
        self::assertSame('/organizations/org-1/attendance/overtime/a1', $this->path($r));
        self::assertTrue($r->lastBody()['enabled']);
    }

    public function testScheduleUpsertUsesPut(): void
    {
        $r = $this->rec();
        $res = new ScheduleResource($r, self::ORG);
        $res->upsert('a1', ['isoYear' => 2026, 'isoWeek' => 26, 'days' => ['mon' => ['09:00-18:00']]]);
        self::assertSame('PUT', $r->lastCall()['method']);
        self::assertSame('/organizations/org-1/attendance/schedules/a1', $this->path($r));
        self::assertSame(2026, $r->lastBody()['isoYear']);

        $res->mySchedule(['isoWeek' => 26]);
        self::assertSame('GET', $r->lastCall()['method']);
        self::assertSame('/organizations/org-1/attendance/my-schedule', $this->path($r));
        self::assertStringContainsString('isoWeek=26', $r->lastCall()['url']);
    }
}
