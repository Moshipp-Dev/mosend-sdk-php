<?php

declare(strict_types=1);

namespace Mosend\Tests;

use Mosend\Exception\MosendWebhookSignatureException;
use Mosend\Webhooks\WebhookVerifier;
use PHPUnit\Framework\TestCase;

final class WebhookVerifierTest extends TestCase
{
    public function testComputeAndVerify(): void
    {
        $body = '{"event":"message.new"}';
        $secret = 'whsec_test';
        $sig = WebhookVerifier::computeSignature($body, $secret);

        self::assertStringStartsWith('sha256=', $sig);
        self::assertTrue(WebhookVerifier::verify($body, $sig, $secret));
        self::assertFalse(WebhookVerifier::verify($body, 'sha256=deadbeef', $secret));
        self::assertFalse(WebhookVerifier::verify($body, null, $secret));
        self::assertFalse(WebhookVerifier::verify($body, $sig, ''));
    }

    public function testParseEventThrowsOnBadSignature(): void
    {
        $this->expectException(MosendWebhookSignatureException::class);
        WebhookVerifier::parseEvent('{}', 'sha256=bad', 'secret');
    }

    public function testParseEventReturnsDecodedArray(): void
    {
        $body = '{"event":"message.new","deliveryId":"d1"}';
        $secret = 's';
        $sig = WebhookVerifier::computeSignature($body, $secret);

        $ev = WebhookVerifier::parseEvent($body, $sig, $secret);
        self::assertSame('message.new', $ev['event']);
        self::assertSame('d1', $ev['deliveryId']);
    }
}
