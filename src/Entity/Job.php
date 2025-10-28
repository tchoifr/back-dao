<?php

namespace App\Entity;

use App\Repository\JobRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: JobRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Job
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $recruiter = null;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(type: Types::TEXT)]
    private string $description;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $category = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $duration = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private array $skills = [];

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private string $budget;

    #[ORM\Column(length: 10)]
    private string $currency = 'WORK';

    #[ORM\Column(length: 20)]
    private string $status = 'open';

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->skills = [];
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    // --- Getters / Setters ---
    public function getId(): ?Uuid { return $this->id; }

    public function getRecruiter(): ?User { return $this->recruiter; }
    public function setRecruiter(?User $recruiter): self { $this->recruiter = $recruiter; return $this; }

    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): self { $this->title = $title; return $this; }

    public function getDescription(): string { return $this->description; }
    public function setDescription(string $description): self { $this->description = $description; return $this; }

    public function getCategory(): ?string { return $this->category; }
    public function setCategory(?string $category): self { $this->category = $category; return $this; }

    public function getDuration(): ?string { return $this->duration; }
    public function setDuration(?string $duration): self { $this->duration = $duration; return $this; }

    public function getSkills(): array { return $this->skills; }
    public function setSkills(?array $skills): self { $this->skills = $skills ?? []; return $this; }

    public function getBudget(): string { return $this->budget; }
    public function setBudget(string $budget): self { $this->budget = $budget; return $this; }

    public function getCurrency(): string { return $this->currency; }
    public function setCurrency(string $currency): self { $this->currency = $currency; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): self { $this->createdAt = $createdAt; return $this; }

    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self { $this->updatedAt = $updatedAt; return $this; }
}
