<?php

namespace TwoIndexNinja\Sdk\Model;

class Project
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $type,
        public readonly string $status,
        public readonly string $createdAt,
        public readonly int $linksTotal,
        public readonly int $inQueue,
        public readonly int $indexed,
        public readonly int $notIndexed,
        public readonly ?string $website,
        public readonly ?string $linksType,
        public readonly ?bool $googleAccountAccessGranted,
        public readonly ?int $linksSendingSpeed,
        public readonly ?int $linksSentGoogle,
        public readonly ?int $linksSentYandex,
        public readonly ?int $linksSentBing,
        public readonly ?int $sentLinks,
        public readonly ?int $linksCheckingSpeed,
        public readonly ?int $checked,
        public readonly ?string $downloadQueueUrl,
        public readonly ?string $downloadSentUrl,
        public readonly ?string $downloadIndexedUrl,
        public readonly ?string $downloadUnindexedUrl,
        public readonly ?string $downloadAllUrl,
        public readonly ?string $downloadCheckedUrl
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['id'],
            $data['name'],
            $data['type'],
            $data['status'],
            $data['created_at'],
            $data['links_total'],
            $data['in_queue'],
            $data['indexed'],
            $data['not_indexed'],
            $data['website'] ?? null,
            $data['links_type'] ?? null,
            isset($data['google_account_access_granted']) ? (bool) $data['google_account_access_granted'] : null,
            $data['links_sending_speed'] ?? null,
            $data['links_sent_google'] ?? null,
            $data['links_sent_yandex'] ?? null,
            $data['links_sent_bing'] ?? null,
            $data['sent_links'] ?? null,
            $data['links_checking_speed'] ?? null,
            $data['checked'] ?? null,
            $data['download_queue_url'] ?? null,
            $data['download_sent_url'] ?? null,
            $data['download_indexed_url'] ?? null,
            $data['download_unindexed_url'] ?? null,
            $data['download_all_url'] ?? null,
            $data['download_checked_url'] ?? null
        );
    }
}