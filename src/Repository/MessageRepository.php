<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;

class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * Retourne tous les messages échangés entre deux utilisateurs
     */

public function findConversation(User $user1, User $user2): array
{
    return $this->createQueryBuilder('m')
        ->andWhere('(m.sender = :u1 AND m.receiver = :u2) OR (m.sender = :u2 AND m.receiver = :u1)')
        ->setParameter('u1', $user1)
        ->setParameter('u2', $user2)
        ->orderBy('m.createdAt', 'ASC')
        ->getQuery()
        ->getResult();
}


    /**
     * Retourne tous les messages reçus par un utilisateur
     */
    public function findReceivedMessages(string $userId): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.receiver = :uid')
            ->setParameter('uid', $userId)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
