<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $walletAddress = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $username = null;

    #[ORM\Column(type: Types::JSON)]
    private array $roles = []; // ✅ un seul champ, au pluriel

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $userToken = null; // 🔐 pour stocker le JWT si besoin

    #[ORM\Column(length: 20)]
    private ?string $network = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 8, nullable: true)]
    private ?string $solBalance = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 8, nullable: true)]
    private ?string $ethBalance = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 8, nullable: true)]
    private ?string $workBalance = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->roles = ['freelance']; // rôle par défaut
    }

    // --- Getters & Setters ---

    public function getId(): ?Uuid { return $this->id; }

    public function getWalletAddress(): ?string { return $this->walletAddress; }
    public function setWalletAddress(string $walletAddress): static { $this->walletAddress = $walletAddress; return $this; }

    public function getUsername(): ?string { return $this->username; }
    public function setUsername(?string $username): static { $this->username = $username; return $this; }

    public function getRoles(): array
    {
        $roles = $this->roles;
        if (empty($roles)) {
            $roles[] = 'freelance';
        }
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = array_values(array_unique($roles));
        return $this;
    }

    public function addRole(string $role): static
    {
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
        return $this;
    }

    public function getUserToken(): ?string { return $this->userToken; }
    public function setUserToken(?string $token): static { $this->userToken = $token; return $this; }

    public function getNetwork(): ?string { return $this->network; }
    public function setNetwork(string $network): static { $this->network = $network; return $this; }

    public function getSolBalance(): ?string { return $this->solBalance; }
    public function setSolBalance(?string $solBalance): static { $this->solBalance = $solBalance; return $this; }

    public function getEthBalance(): ?string { return $this->ethBalance; }
    public function setEthBalance(?string $ethBalance): static { $this->ethBalance = $ethBalance; return $this; }

    public function getWorkBalance(): ?string { return $this->workBalance; }
    public function setWorkBalance(?string $workBalance): static { $this->workBalance = $workBalance; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }

    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static { $this->updatedAt = $updatedAt; return $this; }

    // --- UserInterface (pour JWT et sécurité Symfony) ---
    public function getUserIdentifier(): string { return $this->walletAddress ?? ''; }
    public function eraseCredentials(): void {}
}
