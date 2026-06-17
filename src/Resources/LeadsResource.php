<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/public/leads` — captura de leads (público). */
final class LeadsResource extends Resource
{
    /** @param array<string,mixed> $input name, email, message, phone?, company?, source?, utm? @return mixed */
    public function create(array $input)
    {
        return $this->http->request('POST', '/public/leads', ['body' => $input]);
    }
}
