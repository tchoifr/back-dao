<?php

namespace App\Controller;

use App\Entity\Message;
use App\Repository\ConversationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/messages', name: 'api_messages_')]
class MessageApiController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ConversationRepository $conversationRepository
    ) {}

    // 🟢 1️⃣ Envoyer un message
    #[Route('', name: 'send', methods: ['POST'])]
    public function send(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['conversationUuid']) || empty($data['from']) || empty($data['text'])) {
            return $this->json(['error' => 'Missing required fields'], 400);
        }

        $conversation = $this->conversationRepository->findOneBy(['uuid' => $data['conversationUuid']]);
        if (!$conversation) {
            return $this->json(['error' => 'Conversation not found'], 404);
        }

        $from = strtolower($data['from']);
        if (!in_array($from, ['freelancer', 'employer'])) {
            return $this->json(['error' => 'Invalid sender role'], 400);
        }

        $message = new Message();
        $message->setConversation($conversation);
        $message->setFrom($from);
        $message->setText($data['text']);
        $message->setCreatedAt(new \DateTimeImmutable());
        $message->setReadByEmployer($from === 'employer');
        $message->setReadByFreelancer($from === 'freelancer');

        $this->em->persist($message);
        $this->em->flush();

        return $this->json([
            'id' => $message->getId(),
            'conversationUuid' => $conversation->getUuid(),
            'from' => $message->getFrom(),
            'text' => $message->getText(),
            'createdAt' => $message->getCreatedAt()->format('Y-m-d H:i:s'),
            'readByEmployer' => $message->isReadByEmployer(),
            'readByFreelancer' => $message->isReadByFreelancer(),
        ], 201);
    }

    // 🔵 2️⃣ Récupérer tous les messages d’une conversation
    #[Route('/{uuid}', name: 'list', methods: ['GET'])]
    public function list(string $uuid): JsonResponse
    {
        $conversation = $this->conversationRepository->findOneBy(['uuid' => $uuid]);
        if (!$conversation) {
            return $this->json(['error' => 'Conversation not found'], 404);
        }

        $messages = array_map(function ($msg) {
            return [
                'id' => $msg->getId(),
                'from' => $msg->getFrom(),
                'text' => $msg->getText(),
                'createdAt' => $msg->getCreatedAt()->format('Y-m-d H:i:s'),
                'readByEmployer' => $msg->isReadByEmployer(),
                'readByFreelancer' => $msg->isReadByFreelancer(),
            ];
        }, $conversation->getMessages()->toArray());

        return $this->json([
            'conversationUuid' => $conversation->getUuid(),
            'freelancer' => $conversation->getFreelancer()?->getUsername(),
            'employer' => $conversation->getEmployer()?->getUsername(),
            'project' => $conversation->getProject(),
            'messages' => $messages,
        ]);
    }
}
