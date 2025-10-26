<?php

namespace App\Dto;

use App\Entity\Job;

class JobDto implements \JsonSerializable
{
    public string $id;
    public string $title;
    public string $description;
    public string $budget;
    public string $currency;
    public string $status;
    public string $createdAt;
    public ?string $updatedAt;
    public ?string $recruiterId;
    public ?string $recruiterUsername;
    public ?string $recruiterWalletAddress;

    public static function fromEntity(Job $job): self
    {
        $dto = new self();
        $dto->id = (string) $job->getId();
        $dto->title = $job->getTitle();
        $dto->description = $job->getDescription();
        $dto->budget = $job->getBudget();
        $dto->currency = $job->getCurrency();
        $dto->status = $job->getStatus();
        $dto->createdAt = $job->getCreatedAt()->format('Y-m-d H:i:s');
        $dto->updatedAt = $job->getUpdatedAt()?->format('Y-m-d H:i:s');
        $dto->recruiterId = $job->getRecruiter()?->getId()?->__toString();
        $dto->recruiterUsername = $job->getRecruiter()?->getUsername();
        $dto->recruiterWalletAddress = $job->getRecruiter()?->getWalletAddress();
        return $dto;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
