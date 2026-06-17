<?php

declare(strict_types=1);

namespace Mosend\Tests\Support;

use Mosend\Http\HttpClient;

/**
 * Doble de HttpClient para tests: en vez de pegarle a la red, graba cada
 * request (method, url, opts) y devuelve una respuesta canned. Permite afirmar
 * el path/método/body de cada método de resource sin tocar la red.
 */
final class RecordingHttpClient extends HttpClient
{
    /** @var array<int,array{method:string,url:string,opts:array<string,mixed>,multipart:bool}> */
    public $calls = [];
    /** @var mixed Lo que se devuelve dentro del envelope { data } */
    public $nextData = [];
    /** @var int */
    public $nextStatus = 200;

    public function __construct()
    {
        parent::__construct(['apiKey' => 'mk_live_test.secret', 'baseUrl' => 'https://api.mosend.dev']);
    }

    protected function execute(string $method, string $url, array $opts, bool $isMultipart): array
    {
        $this->calls[] = [
            'method' => $method,
            'url' => $url,
            'opts' => $opts,
            'multipart' => $isMultipart,
        ];
        $body = $this->nextStatus === 204 ? '' : json_encode(['data' => $this->nextData]);
        return [$this->nextStatus, $body === false ? '' : $body, []];
    }

    /** @return array{method:string,url:string,opts:array<string,mixed>,multipart:bool} */
    public function lastCall(): array
    {
        return $this->calls[count($this->calls) - 1];
    }

    /** @return array<string,mixed>|null body JSON-decodeado de la última request */
    public function lastBody(): ?array
    {
        $opts = $this->lastCall()['opts'];
        return isset($opts['body']) ? $opts['body'] : null;
    }
}
