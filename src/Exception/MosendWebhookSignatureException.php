<?php

declare(strict_types=1);

namespace Mosend\Exception;

/** La firma HMAC del webhook entrante no es válida. */
class MosendWebhookSignatureException extends MosendException
{
    public function __construct(string $message = 'Invalid webhook signature')
    {
        parent::__construct($message);
    }
}
