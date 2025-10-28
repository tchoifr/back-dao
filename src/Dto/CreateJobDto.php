<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CreateJobDto
{
    #[Assert\NotBlank]
    public string $recruiterId;

    #[Assert\NotBlank]
    public string $title;

    public ?string $description = null;
    public ?string $category = null;
    public ?string $duration = null;
    public ?array $skills = [];
    public ?string $budget = '0';
    public ?string $currency = 'WORK';
    public ?string $status = 'open';

    public static function fromArray(array $data): self
    {
        $dto = new self();
        foreach ($data as $key => $value) {
            if (property_exists($dto, $key)) {
                $dto->$key = $value;
            }
        }
        return $dto;
    }
}
