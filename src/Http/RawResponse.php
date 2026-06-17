<?php

declare(strict_types=1);

namespace Mosend\Http;

/** Metadata de la última respuesta HTTP (status, request id, rate-limit). */
final class RawResponse
{
    /** @var int */
    public $status;
    /** @var string|null */
    public $requestId;
    /** @var int|null */
    public $rateLimitLimit;
    /** @var int|null */
    public $rateLimitRemaining;
    /** @var int|null */
    public $rateLimitResetSec;

    public function __construct(
        int $status,
        ?string $requestId,
        ?int $rateLimitLimit,
        ?int $rateLimitRemaining,
        ?int $rateLimitResetSec
    ) {
        $this->status = $status;
        $this->requestId = $requestId;
        $this->rateLimitLimit = $rateLimitLimit;
        $this->rateLimitRemaining = $rateLimitRemaining;
        $this->rateLimitResetSec = $rateLimitResetSec;
    }
}
