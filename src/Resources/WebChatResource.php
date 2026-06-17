<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/web-chat` — config server-to-server del web-chat. */
final class WebChatResource extends Resource
{
    /** @return mixed */
    public function listChannels(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/web-chat/channels");
    }

    /** @param array<string,mixed> $input name, color?, ..., orgId? @return mixed */
    public function createChannel(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/web-chat/channels", ['body' => $input]);
    }

    /** @param array<string,mixed> $input @return mixed */
    public function updateChannel(string $id, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/web-chat/channels/{$id}", ['body' => $input]);
    }

    public function deleteChannel(string $id, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('DELETE', "/organizations/{$orgId}/web-chat/channels/{$id}");
    }

    /** @return mixed */
    public function getSnippet(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/web-chat/channels/{$id}/snippet");
    }

    /** @return mixed */
    public function generateIdentitySecret(string $id, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/web-chat/channels/{$id}/identity-secret");
    }

    public function revokeIdentitySecret(string $id, ?string $orgId = null): void
    {
        $orgId = $this->requireOrgId($orgId);
        $this->http->request('DELETE', "/organizations/{$orgId}/web-chat/channels/{$id}/identity-secret");
    }

    /** @param array<string,mixed> $input type?, body?, mediaAssetId?, replyToMessageId?, orgId? @return mixed */
    public function sendToConversation(string $conversationId, array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('POST', "/organizations/{$orgId}/conversations/{$conversationId}/web-chat/send", ['body' => $input]);
    }

    /**
     * @param string|\CURLFile $file
     * @return mixed
     */
    public function uploadMedia(string $conversationId, $file, ?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('POST', "/organizations/{$orgId}/conversations/{$conversationId}/web-chat/media", [
            'multipart' => ['file' => $file],
        ]);
    }
}
