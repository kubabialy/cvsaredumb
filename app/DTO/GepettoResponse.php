<?php

namespace App\DTO;

class GepettoResponse
{
    private function __construct(
        private(set) readonly Personal $personal,

        /**
         * @var Experience[]
         */
        private(set) readonly array $experiences,

        /**
         * @var Education[]
         */
        private(set) readonly array $educations
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            personal: Personal::fromArray($data['personal']),
            experiences: array_map(
                fn(array $experience) => Experience::fromArray($experience),
                $data['experience']
            ),
            educations: array_map(
                fn(array $education) => Education::fromArray($education),
                $data['education']
            )
        );
    }
}
