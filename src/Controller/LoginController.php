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

    // ✅ Normalisation : on met tout en minuscule
    $walletAddress = strtolower(trim($walletAddress));

    // ✅ Recherche insensible à la casse
    $user = $userRepository->createQueryBuilder('u')
        ->where('LOWER(u.walletAddress) = :wallet')
        ->setParameter('wallet', $walletAddress)
        ->getQuery()
        ->getOneOrNullResult();

    if (!$user) {
        return $this->json(['exists' => false]);
    }

    return $this->json([
        'exists' => true,
        'user' => [
            'uuid' => (string) $user->getId(),
            'username' => $user->getUsername(),
            'roles' => $user->getRoles(),
            'walletAddress' => $user->getWalletAddress(),
            'createdAt' => $user->getCreatedAt()?->format('Y-m-d H:i:s'),
        ]
    ]);
}

}
