<?php

namespace App\Dto;

use App\Entity\Message;

class MessageDTO
{
    public string $id;
    public string $senderId;
    public string $receiverId;
    public ?string $senderWallet;
    public ?string $receiverWallet;
    public string $content;
    public bool $isRead;
    public string $createdAt;

    public function __construct(Message $message)
    {
        $this->id = (string) $message->getId();

        $this->senderId = (string) $message->getSender()?->getId();
        $this->receiverId = (string) $message->getReceiver()?->getId();

        $this->senderWallet = $message->getSender()?->getWalletAddress();
        $this->receiverWallet = $message->getReceiver()?->getWalletAddress();

        $this->content = $message->getContent();
        $this->isRead = $message->isRead();

        $this->createdAt = $message->getCreatedAt()->format('Y-m-d H:i:s');
    }
}
