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
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(Request $request, UserRepository $userRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $walletAddress = $data['walletAddress'] ?? null;

        if (!$walletAddress) {
            return $this->json(['error' => 'Wallet address manquante.'], 400);
        }

        $user = $userRepository->findOneBy(['walletAddress' => $walletAddress]);

        // 🟠 Si l’utilisateur n’existe pas encore → front affichera le formulaire de création
        if (!$user) {
            return $this->json(['exists' => false]);
        }

        // 🟢 Si trouvé → on renvoie les infos de session utilisateur
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

    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(Request $request, EntityManagerInterface $em, UserRepository $userRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $walletAddress = $data['walletAddress'] ?? null;
        $username = $data['username'] ?? null;
        $role = $data['role'] ?? null;

        if (!$walletAddress || !$username || !$role) {
            return $this->json(['error' => 'Champs manquants : walletAddress, username ou role.'], 400);
        }

        // 🔍 Vérifie si le wallet existe déjà
        if ($userRepository->findOneBy(['walletAddress' => $walletAddress])) {
            return $this->json(['error' => 'Ce wallet existe déjà.'], 400);
        }

        // 🧱 Création du user
        $user = new User();
        $user->setWalletAddress($walletAddress);
        $user->setUsername($username);

        // ⚙️ Normalisation des rôles
        $normalizedRole = match (strtolower($role)) {
            'freelance' => 'freelance',
            'recruteur', 'employer' => 'employer',
            'admin' => 'admin',
            default => 'freelance'
        };
        $user->setRoles([$normalizedRole]);

        // 🔗 Détecte la blockchain selon le wallet
        $network = str_starts_with($walletAddress, '0x') ? 'Ethereum' : 'Solana';
        $user->setNetwork($network);

        // 💰 Initialisation des soldes
        $user->setSolBalance('0');
        $user->setEthBalance('0');
        $user->setWorkBalance('0');

        $em->persist($user);
        $em->flush();

        return $this->json([
            'message' => 'Utilisateur créé avec succès',
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
