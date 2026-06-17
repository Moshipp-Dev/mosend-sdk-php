<?php

declare(strict_types=1);

namespace Mosend\Tests;

use Mosend\Http\HttpClient;
use Mosend\MosendClient;
use Mosend\Pagination\Paginator;
use PHPUnit\Framework\TestCase;

final class ClientTest extends TestCase
{
    public function testDetectsApiKeyVsBearer(): void
    {
        self::assertTrue(HttpClient::looksLikeApiKey('mk_live_abc.def'));
        self::assertTrue(HttpClient::looksLikeApiKey('mk_test_abc.def'));
        self::assertFalse(HttpClient::looksLikeApiKey('eyJhbGciOiJIUzI1NiJ9.foo.bar'));
    }

    public function testClientExposesResources(): void
    {
        $mosend = new MosendClient(['apiKey' => 'mk_live_x.y', 'orgId' => 'org-1']);
        self::assertInstanceOf(\Mosend\Resources\MessagesResource::class, $mosend->messages);
        self::assertInstanceOf(\Mosend\Resources\ConversationsResource::class, $mosend->conversations);
        self::assertInstanceOf(\Mosend\Resources\HealthResource::class, $mosend->health);
    }

    public function testPaginatorNormalizesEnvelope(): void
    {
        $p = Paginator::page(['data' => [1, 2], 'pageInfo' => ['endCursor' => 'c', 'hasNextPage' => true]]);
        self::assertSame([1, 2], $p['data']);
        self::assertSame('c', $p['endCursor']);
        self::assertTrue($p['hasNextPage']);

        $flat = Paginator::page([1, 2, 3]);
        self::assertSame([1, 2, 3], $flat['data']);
        self::assertFalse($flat['hasNextPage']);
    }

    public function testPaginatorIteratesAcrossPages(): void
    {
        $pages = [
            ['data' => ['a', 'b'], 'pageInfo' => ['endCursor' => 'c1', 'hasNextPage' => true]],
            ['data' => ['c'], 'pageInfo' => ['endCursor' => null, 'hasNextPage' => false]],
        ];
        $i = 0;
        $seenCursors = [];
        $items = iterator_to_array(Paginator::iterate(
            static function (array $params) use (&$i, $pages, &$seenCursors) {
                $seenCursors[] = $params['cursor'] ?? null;
                return $pages[$i++];
            },
            []
        ));

        self::assertSame(['a', 'b', 'c'], $items);
        self::assertSame([null, 'c1'], $seenCursors);
    }
}
