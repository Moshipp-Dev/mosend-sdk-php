<?php

declare(strict_types=1);

namespace Mosend\Exception;

/** 429 Too Many Requests. Expone `retryAfterSec` derivado del header. */
class MosendRateLimitException extends MosendApiException
{
    /** @var int|null */
    protected $retryAfterSec;

    /**
     * @param array{code?:string,body?:mixed,requestId?:?string,path?:?string,metaCode?:?int,metaSubcode?:?int,retryAfterSec?:?int} $extra
     */
    public function __construct(string $message, int $status, array $extra = [])
    {
        parent::__construct($message, $status, $extra);
        $this->retryAfterSec = $extra['retryAfterSec'] ?? null;
    }

    public function getRetryAfterSec(): ?int
    {
        return $this->retryAfterSec;
    }
}
