<?php

declare(strict_types=1);

/**
 * Verificación local sin composer: autoloader PSR-4 mínimo + asserts de la
 * lógica pura (auth, webhooks, errores, paginación). Para los tests reales
 * (PHPUnit) corré `composer install && composer test`.
 *
 * Uso: php scripts/smoke.php
 */
spl_autoload_register(static function (string $class): void {
    $prefix = 'Mosend\\';
    if (strpos($class, $prefix) !== 0) {
        return;
    }
    $rel = str_replace('\\', '/', substr($class, strlen($prefix)));
    $file = __DIR__ . '/../src/' . $rel . '.php';
    if (is_file($file)) {
        require $file;
    }
});

use Mosend\Exception\ErrorFactory;
use Mosend\Exception\MosendAuthException;
use Mosend\Exception\MosendNotFoundException;
use Mosend\Exception\MosendRateLimitException;
use Mosend\Exception\MosendWebhookSignatureException;
use Mosend\Http\HttpClient;
use Mosend\MosendClient;
use Mosend\Pagination\Paginator;
use Mosend\Webhooks\WebhookVerifier;

$tests = 0;
$fails = 0;
function check(string $label, bool $cond): void
{
    global $tests, $fails;
    $tests++;
    if ($cond) {
        echo "  ✓ {$label}\n";
    } else {
        $fails++;
        echo "  ✗ {$label}\n";
    }
}

echo "Auth\n";
check('mk_live_ es API key', HttpClient::looksLikeApiKey('mk_live_a.b'));
check('mk_test_ es API key', HttpClient::looksLikeApiKey('mk_test_a.b'));
check('JWT no es API key', !HttpClient::looksLikeApiKey('eyJ.a.b'));

echo "Webhooks\n";
$body = '{"event":"message.new","deliveryId":"d1"}';
$secret = 'whsec_test';
$sig = WebhookVerifier::computeSignature($body, $secret);
check('computeSignature con prefijo sha256=', strpos($sig, 'sha256=') === 0);
check('verify OK con firma correcta', WebhookVerifier::verify($body, $sig, $secret));
check('verify falla con firma mala', !WebhookVerifier::verify($body, 'sha256=bad', $secret));
check('verify falla sin firma', !WebhookVerifier::verify($body, null, $secret));
$ev = WebhookVerifier::parseEvent($body, $sig, $secret);
check('parseEvent devuelve el evento', ($ev['event'] ?? null) === 'message.new');
$threw = false;
try {
    WebhookVerifier::parseEvent($body, 'sha256=bad', $secret);
} catch (MosendWebhookSignatureException $e) {
    $threw = true;
}
check('parseEvent lanza con firma inválida', $threw);

echo "Errores\n";
check('401 → MosendAuthException', ErrorFactory::build(401, ['message' => 'x'], null, null) instanceof MosendAuthException);
check('404 → MosendNotFoundException', ErrorFactory::build(404, null, null, null) instanceof MosendNotFoundException);
$rl = ErrorFactory::build(429, ['message' => 'slow'], 'req1', 30);
check('429 → MosendRateLimitException', $rl instanceof MosendRateLimitException);
check('429 expone retryAfterSec', $rl instanceof MosendRateLimitException && $rl->getRetryAfterSec() === 30);
$meta = ErrorFactory::build(400, ['message' => 'm', 'error' => 'MetaGraphError', 'metaCode' => 131056], null, null);
check('extrae metaCode', $meta->getMetaCode() === 131056);
check('une mensaje array', ErrorFactory::build(400, ['message' => ['a', 'b']], null, null)->getMessage() === 'a; b');

echo "Paginación\n";
$page = Paginator::page(['data' => [1, 2], 'pageInfo' => ['endCursor' => 'c', 'hasNextPage' => true]]);
check('normaliza envelope', $page['data'] === [1, 2] && $page['endCursor'] === 'c' && $page['hasNextPage'] === true);
$pages = [
    ['data' => ['a', 'b'], 'pageInfo' => ['endCursor' => 'c1', 'hasNextPage' => true]],
    ['data' => ['c'], 'pageInfo' => ['endCursor' => null, 'hasNextPage' => false]],
];
$i = 0;
$items = iterator_to_array(Paginator::iterate(static function (array $p) use (&$i, $pages) {
    return $pages[$i++];
}, []));
check('itera todas las páginas', $items === ['a', 'b', 'c']);

echo "Cliente\n";
$mosend = new MosendClient(['apiKey' => 'mk_live_x.y', 'orgId' => 'org-1']);
check('expone messages', $mosend->messages instanceof \Mosend\Resources\MessagesResource);
check('expone conversations', $mosend->conversations instanceof \Mosend\Resources\ConversationsResource);
check('expone health', $mosend->health instanceof \Mosend\Resources\HealthResource);

echo "\n" . ($fails === 0 ? "✓ TODO OK" : "✗ {$fails} FALLOS") . " ({$tests} checks)\n";
exit($fails === 0 ? 0 : 1);
