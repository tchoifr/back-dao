<?php

namespace App\Dto;

use App\Entity\User;

class UserDto implements \JsonSerializable
{
    public string $id;
    public ?string $walletAddress;
    public ?string $username;
    public ?string $role;
    public ?string $network;
    public ?string $solBalance;
    public ?string $ethBalance;
    public ?string $workBalance;
    public ?string $createdAt;
    public ?string $updatedAt;

    public static function fromEntity(User $user): self
    {
        $dto = new self();
        $dto->id = (string) $user->getId();
        $dto->walletAddress = $user->getWalletAddress();
        $dto->username = $user->getUsername();
        $dto->role = $user->getRole();
        $dto->network = $user->getNetwork();
        $dto->solBalance = $user->getSolBalance();
        $dto->ethBalance = $user->getEthBalance();
        $dto->workBalance = $user->getWorkBalance();
        $dto->createdAt = $user->getCreatedAt()?->format('Y-m-d H:i:s');
        $dto->updatedAt = $user->getUpdatedAt()?->format('Y-m-d H:i:s');

        return $dto;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
