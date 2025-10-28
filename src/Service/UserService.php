<?php

namespace App\Service;

use App\Dto\CreateUserDto;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

class UserService
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function createUserFromDto(CreateUserDto $dto): User
    {
        $user = new User();

        if ($dto->id) {
            $reflection = new \ReflectionProperty(User::class, 'id');
            $reflection->setAccessible(true);
            $reflection->setValue($user, Uuid::fromString($dto->id));
        }

        $user
            ->setWalletAddress($dto->walletAddress)
            ->setUsername($dto->username)
            ->setRole($dto->role)
            ->setUserToken($dto->userToken)
            ->setNetwork($dto->network)
            ->setSolBalance($dto->solBalance)
            ->setEthBalance($dto->ethBalance)
            ->setWorkBalance($dto->workBalance)
            ->setCreatedAt($dto->createdAt ? new \DateTimeImmutable($dto->createdAt) : new \DateTimeImmutable())
            ->setUpdatedAt($dto->updatedAt ? new \DateTimeImmutable($dto->updatedAt) : new \DateTimeImmutable());

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}
