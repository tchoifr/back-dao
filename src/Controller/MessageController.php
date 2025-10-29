<?php

namespace App\Controller;

use App\Dto\MessageDTO;
use App\Entity\Message;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use App\Service\MessageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/messages')]
class MessageController extends AbstractController
{
    public function __construct(
        private MessageService $messageService,
        private UserRepository $userRepository,
        private MessageRepository $messageRepository
    ) {}

    /**
     * 📨 Envoyer un message
     */
    #[Route('/send', name: 'message_send', methods: ['POST'])]
    public function sendMessage(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['senderId'], $data['receiverId'], $data['content'])) {
            return $this->json(['error' => 'Paramètres manquants (senderId, receiverId, content)'], 400);
        }

        $sender = $this->userRepository->find($data['senderId']);
        $receiver = $this->userRepository->find($data['receiverId']);

        if (!$sender || !$receiver) {
            return $this->json(['error' => 'Utilisateur introuvable'], 404);
        }

        $message = $this->messageService->createMessage($sender, $receiver, $data['content']);

        return $this->json(new MessageDTO($message), 201);
    }

    /**
     * 💬 Récupérer une conversation entre deux utilisateurs
     */
    #[Route('/conversation/{user1}/{user2}', name: 'message_conversation', methods: ['GET'])]
    public function getConversation(string $user1, string $user2): JsonResponse
    {
        $u1 = $this->userRepository->find($user1);
        $u2 = $this->userRepository->find($user2);

        if (!$u1 || !$u2) {
            return $this->json(['error' => 'Utilisateur introuvable'], 404);
        }

        // ✅ Marque la conversation comme lue
        $this->messageService->markConversationAsRead($u1, $u2);

        $messages = $this->messageService->getConversation($u1, $u2);
        $dtos = array_map(fn($m) => new MessageDTO($m), $messages);

        return $this->json($dtos);
    }


    /**
     * 📥 Récupérer la boîte de réception d’un utilisateur
     */
    #[Route('/inbox/{userId}', name: 'message_inbox', methods: ['GET'])]
    public function getInbox(string $userId): JsonResponse
    {
        $receiver = $this->userRepository->find($userId);

        if (!$receiver) {
            return $this->json(['error' => 'Utilisateur introuvable'], 404);
        }

        $messages = $this->messageService->getInbox($receiver);
        $dtos = array_map(fn($m) => new MessageDTO($m), $messages);

        return $this->json($dtos);
    }

    /**
     * ✅ Marquer un message comme lu
     */
    #[Route('/read/{messageId}', name: 'message_read', methods: ['PATCH'])]
    public function markAsRead(string $messageId): JsonResponse
    {
        $message = $this->messageRepository->find($messageId);

        if (!$message) {
            return $this->json(['error' => 'Message introuvable'], 404);
        }

        $message->setIsRead(true);
        $this->messageService->save($message);

        return $this->json(new MessageDTO($message));
    }

        /**
     * 🔔 Compter les messages non lus d’un utilisateur
     */
    #[Route('/unread/{userId}', name: 'message_unread', methods: ['GET'])]
    public function getUnreadCount(string $userId): JsonResponse
    {
        $user = $this->userRepository->find($userId);

        if (!$user) {
            return $this->json(['error' => 'Utilisateur introuvable'], 404);
        }

        $count = $this->messageService->countUnreadMessages($user);
        return $this->json(['unreadCount' => $count]);
    }




    /**
     * 🗑️ Supprimer un message
     */
    #[Route('/delete/{messageId}', name: 'message_delete', methods: ['DELETE'])]
    public function deleteMessage(string $messageId): JsonResponse
    {
        $message = $this->messageRepository->find($messageId);

        if (!$message) {
            return $this->json(['error' => 'Message introuvable'], 404);
        }

        $this->messageService->delete($message);

        return $this->json(['success' => true]);
    }
}
