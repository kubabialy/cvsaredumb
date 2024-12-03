<?php

namespace App\DTO;

final class Education
{
    private function __construct(
        private(set) readonly string $institution,
        private(set) readonly string $degree,
        private(set) readonly string $start_date,
        private(set) readonly string $end_date,
    ){}

    public static function fromArray(array $data): self
    {
        return new self(
            institution: $data['institution'],
            degree: $data['degree'],
            start_date: $data['start_date'],
            end_date: $data['end_date'],
        );
    }
}
