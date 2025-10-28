<?php

namespace App\Dto;

use App\Entity\Message;

class MessageDTO
{
    public string $id;
    public string $senderId;
    public string $receiverId;
    public string $content;
    public bool $isRead;
    public string $createdAt;

    public function __construct(Message $message)
    {
        $this->id = $message->getId();
        $this->senderId = $message->getSender()?->getId();
        $this->receiverId = $message->getReceiver()?->getId();
        $this->content = $message->getContent();
        $this->isRead = $message->isRead();
        $this->createdAt = $message->getCreatedAt()->format('Y-m-d H:i:s');
    }
}
