<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\User;
use App\Repository\ConversationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/api/conversations', name: 'api_conversations_')]
class ConversationApiController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ConversationRepository $conversationRepository,
        private UserRepository $userRepository
    ) {}

    // 🟢 1️⃣ Créer une nouvelle conversation
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (
            empty($data['freelancerWallet']) ||
            empty($data['employerWallet']) ||
            empty($data['project'])
        ) {
            return $this->json(['error' => 'Missing required fields'], 400);
        }

        $freelancer = $this->userRepository->findOneBy(['walletAddress' => $data['freelancerWallet']]);
        $employer = $this->userRepository->findOneBy(['walletAddress' => $data['employerWallet']]);

        if (!$freelancer || !$employer) {
            return $this->json(['error' => 'Invalid freelancer or employer wallet'], 404);
        }

        $conversation = new Conversation();
        $conversation->setUuid(Uuid::v4()->toRfc4122());
        $conversation->setFreelancer($freelancer);
        $conversation->setEmployer($employer);
        $conversation->setProject($data['project']);
        $conversation->setActive(true);

        $this->em->persist($conversation);
        $this->em->flush();

        return $this->json([
            'uuid' => $conversation->getUuid(),
            'freelancer' => $freelancer->getUsername(),
            'employer' => $employer->getUsername(),
            'project' => $conversation->getProject(),
            'active' => $conversation->isActive(),
            'messages' => [],
        ], 201);
    }

    // 🟣 2️⃣ Lister toutes les conversations d’un utilisateur
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $userUuid = $request->query->get('userUuid');
        if (!$userUuid) {
            return $this->json(['error' => 'Missing userUuid parameter'], 400);
        }

        $user = $this->userRepository->findOneBy(['walletAddress' => $userUuid]);
        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }

        $conversations = $this->conversationRepository->findByUser($user);

        $data = array_map(function (Conversation $conv) {
            return [
                'uuid' => $conv->getUuid(),
                'freelancer' => $conv->getFreelancer()?->getUsername(),
                'employer' => $conv->getEmployer()?->getUsername(),
                'project' => $conv->getProject(),
                'active' => $conv->isActive(),
                'messageCount' => count($conv->getMessages()),
            ];
        }, $conversations);

        return $this->json($data);
    }

    // 🔵 3️⃣ Voir une conversation précise
    #[Route('/{uuid}', name: 'show', methods: ['GET'])]
    public function show(string $uuid): JsonResponse
    {
        $conversation = $this->conversationRepository->findOneBy(['uuid' => $uuid]);

        if (!$conversation) {
            return $this->json(['error' => 'Conversation not found'], 404);
        }

        $messages = [];
        foreach ($conversation->getMessages() as $msg) {
            $messages[] = [
                'from' => $msg->getFrom(),
                'text' => $msg->getText(),
                'createdAt' => $msg->getCreatedAt()->format('Y-m-d H:i:s'),
                'readByEmployer' => $msg->isReadByEmployer(),
                'readByFreelancer' => $msg->isReadByFreelancer(),
            ];
        }

        return $this->json([
            'uuid' => $conversation->getUuid(),
            'freelancer' => $conversation->getFreelancer()?->getUsername(),
            'employer' => $conversation->getEmployer()?->getUsername(),
            'project' => $conversation->getProject(),
            'active' => $conversation->isActive(),
            'messages' => $messages,
        ]);
    }
}
