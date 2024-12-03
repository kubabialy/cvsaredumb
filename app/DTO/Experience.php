<?php

namespace App\DTO;

final class Experience
{
    private function __construct(
        private(set) readonly string $company,
        private(set) readonly string $position,
        private(set) readonly string $start_date,
        private(set) readonly string $end_date,
        private(set) readonly string $description
    ){}

    public static function fromArray(array $data): self
    {
        return new self(
            company: $data['company'],
            position: $data['position'],
            start_date: $data['start_date'],
            end_date: $data['end_date'],
            description: $data['description']
        );
    }
}
