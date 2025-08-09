<?php

namespace TwoIndexNinja\Sdk\Model;

class LinkSource
{
    public function __construct(
        public readonly int $id,
        public readonly int $projectId,
        public readonly string $name,
        public readonly string $type,
        public readonly string $createdAt,
        public readonly string $status,
        public readonly bool $isPending,
        public readonly bool $watch,
        public readonly bool $googleAccessGranted,
        public readonly bool $isExternalLinks,
        public readonly array $searchEngines,
        public readonly ?string $processingDate,
        public readonly ?bool $hasError,
        public readonly ?string $errorMessage,
        public readonly ?bool $isSuccess,
        public readonly ?int $totalLinks,
        public readonly ?int $addedLinks,
        public readonly ?int $invalidLinks
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['project_id'],
            $data['name'],
            $data['type'],
            $data['created_at'],
            $data['status'],
            (bool) $data['is_pending'],
            (bool) $data['watch'],
            (bool) $data['google_access_granted'],
            (bool) $data['is_external_links'],
            (array) $data['search_engines'],
            $data['processing_date'] ?? null,
            $data['has_error'] ?? null,
            $data['error_message'] ?? null,
            $data['is_success'] ?? null,
            $data['total_links'] ?? null,
            $data['added_links'] ?? null,
            $data['invalid_links'] ?? null
        );
    }
}