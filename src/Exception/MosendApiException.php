<?php

declare(strict_types=1);

namespace Mosend\Exception;

/**
 * Respuesta 4xx/5xx con un cuerpo de error del backend. Las subclases por
 * código HTTP (400/401/.../5xx) heredan de esta. Los errores originados en
 * Meta Graph traen `metaCode`/`metaSubcode`.
 */
class MosendApiException extends MosendException
{
    /** @var int */
    protected $status;
    /** @var string */
    protected $errorCode;
    /** @var array<string,mixed>|string|null */
    protected $body;
    /** @var string|null */
    protected $requestId;
    /** @var string|null */
    protected $path;
    /** @var int|null */
    protected $metaCode;
    /** @var int|null */
    protected $metaSubcode;

    /**
     * @param array{code?:string,body?:mixed,requestId?:?string,path?:?string,metaCode?:?int,metaSubcode?:?int} $extra
     */
    public function __construct(string $message, int $status, array $extra = [])
    {
        parent::__construct($message, $status);
        $this->status = $status;
        $this->errorCode = $extra['code'] ?? 'api_error';
        $this->body = $extra['body'] ?? null;
        $this->requestId = $extra['requestId'] ?? null;
        $this->path = $extra['path'] ?? null;
        $this->metaCode = $extra['metaCode'] ?? null;
        $this->metaSubcode = $extra['metaSubcode'] ?? null;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    /** @return array<string,mixed>|string|null */
    public function getBody()
    {
        return $this->body;
    }

    public function getRequestId(): ?string
    {
        return $this->requestId;
    }

    public function getMetaCode(): ?int
    {
        return $this->metaCode;
    }

    public function getMetaSubcode(): ?int
    {
        return $this->metaSubcode;
    }
}
