<?php

declare(strict_types=1);

namespace Mosend\Tests;

use Mosend\MosendClient;
use Mosend\Resources\AuthResource;
use Mosend\Resources\CreditNotesResource;
use Mosend\Resources\MembershipsResource;
use Mosend\Resources\PlansResource;
use Mosend\Resources\PushResource;
use Mosend\Resources\RolesResource;
use Mosend\Resources\WebChatPublicResource;
use Mosend\Tests\Support\RecordingHttpClient;
use PHPUnit\Framework\TestCase;

final class Phase2Test extends TestCase
{
    private const ORG = 'org-1';

    private function path(RecordingHttpClient $r): string
    {
        return parse_url($r->lastCall()['url'], PHP_URL_PATH) ?: '';
    }

    public function testClientExposesAll57Resources(): void
    {
        $mosend = new MosendClient(['apiKey' => 'mk_live_x.y', 'orgId' => self::ORG]);
        $expected = [
            'addons', 'aiCredits', 'apiKeys', 'audit', 'auth', 'autoReplies', 'billing',
            'botConfig', 'botEvents', 'broadcasts', 'contactLists', 'contacts',
            'conversations', 'creditNotes', 'flows', 'health', 'integrations', 'invitations',
            'invoices', 'knowledge', 'leads', 'media', 'memberships', 'mercadoPago', 'messages',
            'notifications', 'optIns', 'orgAiProviders', 'organizations', 'passkeys',
            'paymentMethods', 'permissions', 'phoneNumbers', 'planLimits', 'plans', 'pricing',
            'profiles', 'push', 'quickReplies', 'reactions', 'reports', 'roles', 'stickers',
            'systemNotices', 'tags', 'tasks', 'templates', 'twoFactor', 'usage', 'users', 'waba',
            'wallet', 'walletAlerts', 'webChat', 'webChatPublic', 'webhooksOutbound', 'whatsappLinks',
        ];
        self::assertCount(57, $expected);
        foreach ($expected as $prop) {
            self::assertObjectHasProperty($prop, $mosend);
            self::assertNotNull($mosend->{$prop}, "resource {$prop} no instanciado");
        }
    }

    /** Paths "raros" que no siguen el patrón /organizations/{orgId}/<recurso>. */
    public function testTrickyPaths(): void
    {
        $r = new RecordingHttpClient();

        (new AuthResource($r))->login(['email' => 'a@b.com', 'password' => 'x']);
        self::assertSame('/auth/login', $this->path($r));

        (new PlansResource($r, self::ORG))->change(['toPlanSlug' => 'pro']);
        self::assertSame('PATCH', $r->lastCall()['method']);
        self::assertSame('/plans/organizations/org-1/plan', $this->path($r));

        (new PlansResource($r, self::ORG))->previewChange(['toPlanSlug' => 'pro']);
        self::assertSame('/plans/organizations/org-1/preview-change', $this->path($r));

        (new CreditNotesResource($r))->create(['organizationId' => 'o', 'amount' => 1, 'currency' => 'COP', 'reason' => 'x', 'applyToWallet' => true]);
        self::assertSame('/admin/credit-notes', $this->path($r));

        (new MembershipsResource($r, self::ORG))->setWabaScope('m1', ['wabaIds' => []]);
        self::assertSame('PUT', $r->lastCall()['method']);
        self::assertSame('/organizations/org-1/memberships/m1/waba-scope', $this->path($r));

        (new RolesResource($r, self::ORG))->setPermissions('r1', ['permissions' => ['messages:send']]);
        self::assertSame('/organizations/org-1/roles/r1/permissions', $this->path($r));

        (new PushResource($r))->subscribe(['endpoint' => 'e', 'p256dh' => 'k', 'auth' => 'a']);
        self::assertSame('/push/subscribe', $this->path($r));

        (new WebChatPublicResource($r))->createSession('tok', ['visitorId' => 'v', 'mode' => 'anonymous']);
        self::assertSame('/web-chat/tok/sessions', $this->path($r));
    }
}
