# moshipp/mosend-sdk (PHP)

> SDK oficial de PHP para la **API REST de Mosend** — WhatsApp Business, Web Chat, Bot, Billing y Webhooks.

- **Cero dependencias** — solo `ext-curl` y `ext-json` (ideal para WooCommerce / hosting compartido).
- **PHP 7.4+**.
- Auth automática (API key `X-Api-Key` o Bearer JWT), errores tipados, paginación por cursor y verificación de webhooks HMAC.

**Cobertura completa**: los 65 resources de la API (mismo mapa que el SDK de TypeScript, verificado 1:1 contra el OpenAPI del backend). Pendiente solo la publicación a Packagist (próxima fase).

## Instalación

```bash
composer require moshipp/mosend-sdk
```

## Uso

```php
use Mosend\MosendClient;

$mosend = new MosendClient([
    'apiKey' => getenv('MOSEND_API_KEY'),   // mk_live_<prefix>.<secret>
    'orgId'  => getenv('MOSEND_ORG_ID'),    // UUID de tu organización
]);

// Enviar una plantilla
$msg = $mosend->messages->send([
    'phoneNumberId' => '<phone-uuid>',
    'to'            => '573001234567',       // E.164 sin '+'
    'type'          => 'template',
    'templateId'    => '<uuid-de-la-plantilla>',
    'variables'     => ['Juan', 'FAC-2026-0042'],
]);
echo $msg['id'], ' ', $msg['metaMessageId'], PHP_EOL;

// Paginar conversaciones (maneja el cursor por vos)
foreach ($mosend->conversations->iterate(['status' => 'open']) as $conv) {
    echo $conv['id'], PHP_EOL;
}
```

## Errores

```php
use Mosend\Exception\MosendRateLimitException;
use Mosend\Exception\MosendApiException;

try {
    $mosend->messages->send([...]);
} catch (MosendRateLimitException $e) {
    error_log('rate limit; reintentar en ' . $e->getRetryAfterSec() . 's');
} catch (MosendApiException $e) {
    error_log($e->getStatus() . ' ' . $e->getMessage() . ' meta=' . $e->getMetaCode());
}
```

## Webhooks

```php
use Mosend\Webhooks\WebhookVerifier;
use Mosend\Exception\MosendWebhookSignatureException;

$raw = file_get_contents('php://input');           // body CRUDO
$sig = $_SERVER['HTTP_X_MOSEND_SIGNATURE'] ?? null;

try {
    $event = WebhookVerifier::parseEvent($raw, $sig, getenv('MOSEND_WEBHOOK_SECRET'));
    // $event['event'] => 'message.new' | 'message.status' | ...
    // deduplicá por $event['deliveryId']
} catch (MosendWebhookSignatureException $e) {
    http_response_code(401);
    exit;
}
```

## Idempotencia y reintentos

```php
$mosend->messages->send([...], ['idempotencyKey' => 'order-42-greeting']);

$mosend = new MosendClient([
    'apiKey'  => '...',
    'orgId'   => '...',
    'retries' => ['max' => 3, 'on' => [429, 502, 503]],   // opt-in, con backoff
]);
```

## Desarrollo

```bash
composer install
composer test          # PHPUnit
php scripts/smoke.php   # verificación rápida sin composer
```

## Licencia

MIT © Moshipp SAS
