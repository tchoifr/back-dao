<?php

namespace App\Entity;

use App\Repository\ConversationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ConversationRepository::class)]
class Conversation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 36, unique: true)]
    private string $uuid;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'])]
    private ?User $freelancer = null;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'])]
    private ?User $employer = null;

    #[ORM\Column(length: 255)]
    private string $project;

    #[ORM\Column]
    private bool $active = true;

    #[ORM\OneToMany(mappedBy: 'conversation', targetEntity: Message::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $messages;

    public function __construct()
    {
        $this->uuid = Uuid::v4()->toRfc4122();
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getUuid(): string { return $this->uuid; }
    public function setUuid(string $uuid): self { $this->uuid = $uuid; return $this; }

    public function getFreelancer(): ?User { return $this->freelancer; }
    public function setFreelancer(?User $freelancer): self { $this->freelancer = $freelancer; return $this; }

    public function getEmployer(): ?User { return $this->employer; }
    public function setEmployer(?User $employer): self { $this->employer = $employer; return $this; }

    public function getProject(): string { return $this->project; }
    public function setProject(string $project): self { $this->project = $project; return $this; }

    public function isActive(): bool { return $this->active; }
    public function setActive(bool $active): self { $this->active = $active; return $this; }

    public function getMessages(): Collection { return $this->messages; }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setConversation($this);
        }
        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            if ($message->getConversation() === $this) {
                $message->setConversation(null);
            }
        }
        return $this;
    }
}
