<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CreateUserDto
{
    #[Assert\Uuid]
    public ?string $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $walletAddress;

    #[Assert\Length(max: 100)]
    public ?string $username = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    public string $role;

    #[Assert\Length(max: 255)]
    public ?string $userToken = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    public string $network;

    #[Assert\Type('numeric')]
    public ?string $solBalance = null;

    #[Assert\Type('numeric')]
    public ?string $ethBalance = null;

    #[Assert\Type('numeric')]
    public ?string $workBalance = null;

    #[Assert\DateTime]
    public ?string $createdAt = null;

    #[Assert\DateTime]
    public ?string $updatedAt = null;
}
