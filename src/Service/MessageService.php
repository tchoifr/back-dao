<?php

namespace App\Service;

use App\Entity\Message;
use App\Entity\User;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;

class MessageService
{
    public function __construct(
        private EntityManagerInterface $em,
        private MessageRepository $messageRepository
    ) {}

    public function createMessage(User $sender, User $receiver, string $content): Message
    {
        $message = new Message();
        $message->setSender($sender);
        $message->setReceiver($receiver);
        $message->setContent($content);

        $this->em->persist($message);
        $this->em->flush();

        return $message;
    }

   public function getConversation(User $u1, User $u2): array
{
    return $this->messageRepository->findConversation($u1, $u2);
}
    public function getInbox(User $receiver): array
    {
        return $this->messageRepository->findReceivedMessages($receiver->getId());
    }

public function save(Message $message): void
{
    $this->em->persist($message);
    $this->em->flush();
}

public function delete(Message $message): void
{
    $this->em->remove($message);
    $this->em->flush();
}

}
