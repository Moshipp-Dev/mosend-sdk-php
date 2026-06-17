<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/stickers` — stickers. */
final class StickersResource extends Resource
{
    /** @return mixed */
    public function list(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/stickers");
    }

    /**
     * @param string|\CURLFile $file
     * @return mixed
     */
    public function upload($file, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/stickers/upload", ['multipart' => ['file' => $file]]);
    }

    /** @return mixed */
    public function fromMessage(string $messageId, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/stickers/from-message/{$messageId}");
    }

    /** @param array<string,mixed> $input phoneNumberId, to, replyToMessageId?, orgId? @return mixed */
    public function send(string $stickerId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/stickers/{$stickerId}/send", ['body' => $input]);
    }

    public function delete(string $id, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('DELETE', "/organizations/{$orgId}/stickers/{$id}");
    }
}
