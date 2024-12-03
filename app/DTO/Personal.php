<?php

namespace App\DTO;

final class Personal
{
    private function __construct(
        private(set) readonly string $name,
        private(set) readonly string $email,
        private(set) readonly ?string $linkedin,
        private(set) readonly string $summary
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            linkedin: $data['linkedin'] ?? null,
            summary: $data['summary']
        );
    }
}
