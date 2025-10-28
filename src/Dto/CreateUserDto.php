<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CreateUserDto
{
    #[Assert\NotBlank(message: "Le walletAddress est requis.")]
    public ?string $walletAddress = null;

    #[Assert\NotBlank(message: "Le username est requis.")]
    public ?string $username = null;

    #[Assert\NotBlank(message: "Le réseau est requis.")]
    public ?string $network = null;

    #[Assert\Type('array')]
    #[Assert\NotBlank(message: "Au moins un rôle est requis.")]
    public array $roles = [];

    public ?string $solBalance = null;
    public ?string $ethBalance = null;
    public ?string $workBalance = null;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->walletAddress = $data['walletAddress'] ?? null;
        $dto->username = $data['username'] ?? null;
        $dto->network = $data['network'] ?? null;
        $dto->roles = $data['roles'] ?? [];
        $dto->solBalance = $data['solBalance'] ?? '0';
        $dto->ethBalance = $data['ethBalance'] ?? '0';
        $dto->workBalance = $data['workBalance'] ?? '0';
        return $dto;
    }
}
