<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/admin/credit-notes` — notas de crédito (staff). */
final class CreditNotesResource extends Resource
{
    /** @param array<string,mixed> $input organizationId, amount, currency, reason, applyToWallet, invoiceId? @return mixed */
    public function create(array $input)
    {
        return $this->http->request('POST', '/admin/credit-notes', ['body' => $input]);
    }

    /** @param array<string,mixed> $query @return mixed */
    public function list(array $query = [])
    {
        return $this->http->request('GET', '/admin/credit-notes', ['query' => $query]);
    }

    /** @return mixed */
    public function retrieve(string $id)
    {
        return $this->http->request('GET', "/admin/credit-notes/{$id}");
    }

    /** @return mixed */
    public function void(string $id)
    {
        return $this->http->request('POST', "/admin/credit-notes/{$id}/void");
    }

    /** @return mixed */
    public function pdf(string $id)
    {
        return $this->http->request('GET', "/admin/credit-notes/{$id}/pdf");
    }

    /** @return mixed */
    public function regeneratePdf(string $id)
    {
        return $this->http->request('POST', "/admin/credit-notes/{$id}/regenerate-pdf");
    }
}
