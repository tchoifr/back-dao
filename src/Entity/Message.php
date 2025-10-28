<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Conversation::class, inversedBy: 'messages')]
    private ?Conversation $conversation = null;

    // ✅ On renomme la colonne pour éviter le mot réservé SQL
    #[ORM\Column(name: 'sender_from', length: 20)]
    private string $from; // 'freelancer' ou 'employer'

    #[ORM\Column(type: 'text')]
    private string $text;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    private bool $readByEmployer = false;

    #[ORM\Column]
    private bool $readByFreelancer = false;

    // Getters & Setters
    public function getId(): ?int { return $this->id; }

    public function getConversation(): ?Conversation { return $this->conversation; }
    public function setConversation(?Conversation $conversation): self { $this->conversation = $conversation; return $this; }

    public function getFrom(): string { return $this->from; }
    public function setFrom(string $from): self { $this->from = $from; return $this; }

    public function getText(): string { return $this->text; }
    public function setText(string $text): self { $this->text = $text; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): self { $this->createdAt = $createdAt; return $this; }

    public function isReadByEmployer(): bool { return $this->readByEmployer; }
    public function setReadByEmployer(bool $readByEmployer): self { $this->readByEmployer = $readByEmployer; return $this; }

    public function isReadByFreelancer(): bool { return $this->readByFreelancer; }
    public function setReadByFreelancer(bool $readByFreelancer): self { $this->readByFreelancer = $readByFreelancer; return $this; }
}
