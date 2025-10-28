<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api', name: 'api_')]
class AuthController extends AbstractController
{
    #[Route('/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request, UserRepository $userRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $walletAddress = $data['walletAddress'] ?? null;

        if (!$walletAddress) {
            return $this->json(['error' => 'Wallet address manquante.'], 400);
        }

        $user = $userRepository->findOneBy(['walletAddress' => $walletAddress]);

        if (!$user) {
            return $this->json(['exists' => false]);
        }

        // 🔐 Ici, on pourra plus tard générer un JWT
        // pour l’instant, on retourne les infos utilisateur
        return $this->json([
            'exists' => true,
            'user' => [
                'id' => (string) $user->getId(),
                'walletAddress' => $user->getWalletAddress(),
                'username' => $user->getUsername(),
                'roles' => $user->getRoles(),
                'network' => $user->getNetwork(),
                'solBalance' => $user->getSolBalance(),
                'ethBalance' => $user->getEthBalance(),
                'workBalance' => $user->getWorkBalance(),
                'createdAt' => $user->getCreatedAt()?->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    #[Route('/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request, EntityManagerInterface $em, UserRepository $userRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $walletAddress = $data['walletAddress'] ?? null;
        $username = $data['username'] ?? null;
        $roles = $data['roles'] ?? null;

        if (!$walletAddress || !$username || !$roles) {
            return $this->json(['error' => 'Champs manquants : walletAddress, username ou roles.'], 400);
        }

        if (!is_array($roles)) {
            return $this->json(['error' => 'Le champ roles doit être un tableau (ex: ["admin"]).'], 400);
        }

        // 🚫 Vérifier si le wallet existe déjà
        if ($userRepository->findOneBy(['walletAddress' => $walletAddress])) {
            return $this->json(['error' => 'Ce wallet existe déjà.'], 400);
        }

        // 🧱 Création de l'utilisateur
        $user = new User();
        $user->setWalletAddress($walletAddress);
        $user->setUsername($username);
        $user->setRoles($roles);
        $user->setNetwork(str_starts_with($walletAddress, '0x') ? 'ethereum' : 'solana');
        $user->setSolBalance('0');
        $user->setEthBalance('0');
        $user->setWorkBalance('0');

        $em->persist($user);
        $em->flush();

        return $this->json([
            'message' => '✅ Utilisateur créé avec succès',
            'user' => [
                'id' => (string) $user->getId(),
                'walletAddress' => $user->getWalletAddress(),
                'username' => $user->getUsername(),
                'roles' => $user->getRoles(),
                'network' => $user->getNetwork(),
                'solBalance' => $user->getSolBalance(),
                'ethBalance' => $user->getEthBalance(),
                'workBalance' => $user->getWorkBalance(),
                'createdAt' => $user->getCreatedAt()?->format('Y-m-d H:i:s'),
            ]
        ], 201);
    }
}
