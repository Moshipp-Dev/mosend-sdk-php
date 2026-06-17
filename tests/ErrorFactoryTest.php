<?php

declare(strict_types=1);

namespace Mosend\Tests;

use Mosend\Exception\ErrorFactory;
use Mosend\Exception\MosendAuthException;
use Mosend\Exception\MosendNotFoundException;
use Mosend\Exception\MosendRateLimitException;
use Mosend\Exception\MosendServerException;
use PHPUnit\Framework\TestCase;

final class ErrorFactoryTest extends TestCase
{
    public function testMapsStatusToSubclass(): void
    {
        self::assertInstanceOf(MosendAuthException::class, ErrorFactory::build(401, ['message' => 'no'], null, null));
        self::assertInstanceOf(MosendNotFoundException::class, ErrorFactory::build(404, null, null, null));
        self::assertInstanceOf(MosendServerException::class, ErrorFactory::build(503, null, null, null));
    }

    public function testRateLimitCarriesRetryAfter(): void
    {
        $e = ErrorFactory::build(429, ['message' => 'slow'], 'req1', 30);
        self::assertInstanceOf(MosendRateLimitException::class, $e);
        self::assertSame(30, $e->getRetryAfterSec());
        self::assertSame('req1', $e->getRequestId());
        self::assertSame(429, $e->getStatus());
    }

    public function testExtractsMetaCodes(): void
    {
        $e = ErrorFactory::build(400, [
            'message' => 'meta error',
            'error' => 'MetaGraphError',
            'metaCode' => 131056,
            'metaSubcode' => 2,
        ], null, null);
        self::assertSame(131056, $e->getMetaCode());
        self::assertSame(2, $e->getMetaSubcode());
        self::assertSame('MetaGraphError', $e->getErrorCode());
    }

    public function testJoinsArrayMessage(): void
    {
        $e = ErrorFactory::build(400, ['message' => ['campo a', 'campo b']], null, null);
        self::assertSame('campo a; campo b', $e->getMessage());
    }

    public function testFallsBackToHttpStatusMessage(): void
    {
        $e = ErrorFactory::build(418, null, null, null);
        self::assertSame('HTTP 418', $e->getMessage());
    }
}
