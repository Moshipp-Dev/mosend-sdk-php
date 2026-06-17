<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations` — organizaciones. */
final class OrganizationsResource extends Resource
{
    /** @return mixed */
    public function list()
    {
        return $this->http->request('GET', '/organizations');
    }

    /** @param array<string,mixed> $input name, slug, billingEmail, country?, currency?, timezone? @return mixed */
    public function create(array $input)
    {
        return $this->http->request('POST', '/organizations', ['body' => $input]);
    }

    /** @return mixed */
    public function suggestSlug(string $name)
    {
        return $this->http->request('GET', '/organizations/slug-suggest', ['query' => ['name' => $name]]);
    }

    /** @return mixed */
    public function isSlugAvailable(string $slug)
    {
        return $this->http->request('GET', '/organizations/slug-available', ['query' => ['slug' => $slug]]);
    }

    /** @return mixed */
    public function retrieve(string $id)
    {
        return $this->http->request('GET', "/organizations/{$id}");
    }

    /** @param array<string,mixed> $input @return mixed */
    public function update(string $id, array $input)
    {
        return $this->http->request('PATCH', "/organizations/{$id}", ['body' => $input]);
    }
}
