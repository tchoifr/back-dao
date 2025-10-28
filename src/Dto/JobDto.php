<?php

namespace App\Dto;

use App\Entity\Job;

class JobDto implements \JsonSerializable
{
    public string $id;
    public string $title;
    public string $description;
    public ?string $category;
    public ?string $duration;
    public array $skills;
    public string $budget;
    public string $currency;
    public string $status;
    public string $createdAt;
    public ?string $updatedAt;
    public string $recruiterId;
    public ?string $recruiterUsername;

    public static function fromEntity(Job $job): self
    {
        $dto = new self();
        $dto->id = (string) $job->getId();
        $dto->title = $job->getTitle();
        $dto->description = $job->getDescription();
        $dto->category = $job->getCategory();
        $dto->duration = $job->getDuration();
        $dto->skills = $job->getSkills();
        $dto->budget = $job->getBudget();
        $dto->currency = $job->getCurrency();
        $dto->status = $job->getStatus();
        $dto->createdAt = $job->getCreatedAt()->format('Y-m-d H:i:s');
        $dto->updatedAt = $job->getUpdatedAt()?->format('Y-m-d H:i:s');
        $dto->recruiterId = (string) $job->getRecruiter()?->getId();
        $dto->recruiterUsername = $job->getRecruiter()?->getUsername();

        return $dto;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
