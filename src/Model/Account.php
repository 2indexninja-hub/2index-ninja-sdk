<?php

namespace TwoIndexNinja\Sdk\Model;

class Account
{
    public function __construct(
        public readonly string $email,
        public readonly string $tariff,
        public readonly float $balance,
        public readonly int $availableProjects,
        public readonly int $availableLinks,
        public readonly int $availableIndexationCheckLinks,
        public readonly int $linkSendingSpeed,
        public readonly bool $isTariffAvailable,
        public readonly ?string $tariffExpiringDate,
        public readonly bool $isEmailVerified,
        public readonly string $linkCost
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['email'],
            $data['tariff'],
            (float) $data['balance'],
            $data['available_projects'],
            $data['available_links'],
            $data['available_indexation_check_links'],
            $data['link_sending_speed'],
            $data['tariff_available'],
            $data['tariff_expiring_date'],
            $data['email_verified'],
            $data['link_cost']
        );
    }
}
