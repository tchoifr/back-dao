<?php

namespace App\Entity;

use App\Repository\ContractRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ContractRepository::class)]
class Contract
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    private ?string $contractAddress = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $recruiter = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $freelancer = null;

    #[ORM\ManyToOne(targetEntity: Job::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Job $job = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 8)]
    private ?string $amount = null;

    #[ORM\Column(length: 10)]
    private ?string $currency = null;

    #[ORM\Column(length: 20)]
    private ?string $status = null;

    #[ORM\Column(nullable: true)]
    private ?bool $signedByFreelancer = null;

    #[ORM\Column(nullable: true)]
    private ?bool $signedByRecruiter = null;

    #[ORM\Column(nullable: true)]
    private ?bool $signedByDao = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct() { $this->createdAt = new \DateTimeImmutable(); }

    public function getId(): ?Uuid { return $this->id; }
    public function getContractAddress(): ?string { return $this->contractAddress; }
    public function setContractAddress(string $contractAddress): static { $this->contractAddress = $contractAddress; return $this; }
    public function getRecruiter(): ?User { return $this->recruiter; }
    public function setRecruiter(?User $recruiter): static { $this->recruiter = $recruiter; return $this; }
    public function getFreelancer(): ?User { return $this->freelancer; }
    public function setFreelancer(?User $freelancer): static { $this->freelancer = $freelancer; return $this; }
    public function getJob(): ?Job { return $this->job; }
    public function setJob(?Job $job): static { $this->job = $job; return $this; }
    public function getAmount(): ?string { return $this->amount; }
    public function setAmount(string $amount): static { $this->amount = $amount; return $this; }
    public function getCurrency(): ?string { return $this->currency; }
    public function setCurrency(string $currency): static { $this->currency = $currency; return $this; }
    public function getStatus(): ?string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }
    public function isSignedByFreelancer(): ?bool { return $this->signedByFreelancer; }
    public function setSignedByFreelancer(?bool $signedByFreelancer): static { $this->signedByFreelancer = $signedByFreelancer; return $this; }
    public function isSignedByRecruiter(): ?bool { return $this->signedByRecruiter; }
    public function setSignedByRecruiter(?bool $signedByRecruiter): static { $this->signedByRecruiter = $signedByRecruiter; return $this; }
    public function isSignedByDao(): ?bool { return $this->signedByDao; }
    public function setSignedByDao(?bool $signedByDao): static { $this->signedByDao = $signedByDao; return $this; }
    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }
    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static { $this->updatedAt = $updatedAt; return $this; }
}
