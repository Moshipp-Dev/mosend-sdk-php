<?php

declare(strict_types=1);

namespace Mosend\Tests;

use Mosend\Resources\BroadcastsResource;
use Mosend\Resources\ContactListsResource;
use Mosend\Resources\ContactsResource;
use Mosend\Resources\MessagesResource;
use Mosend\Resources\OptInsResource;
use Mosend\Resources\QuickRepliesResource;
use Mosend\Resources\ReactionsResource;
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
}
