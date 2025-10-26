<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class LoginController extends AbstractController
{
    #[Route('/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request, UserRepository $userRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $walletAddress = $data['walletAddress'] ?? null;

        if (!$walletAddress) {
            return $this->json(['error' => 'Wallet address required'], 400);
        }

        $user = $userRepository->findOneBy(['walletAddress' => $walletAddress]);

        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }

        // Tu pourrais générer un token JWT ici pour plus tard
        return $this->json([
            'uuid' => $user->getId(),
            'username' => $user->getUsername(),
            'role' => $user->getRole(),
            'walletAddress' => $user->getWalletAddress(),
            'createdAt' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
        ]);
    }
}
