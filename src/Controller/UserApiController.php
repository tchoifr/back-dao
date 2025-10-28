<?php

namespace App\Controller;

use App\Dto\UserDto;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/users', name: 'api_users_')]
class UserApiController extends AbstractController
{
    #[Route('', name: 'get_all', methods: ['GET'])]
    public function getAll(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();
        $dtos = array_map(fn(User $user) => UserDto::fromEntity($user), $users);

        return $this->json($dtos);
    }

    #[Route('/{id}', name: 'get_one', methods: ['GET'])]
    public function getOne(string $id, UserRepository $userRepository): JsonResponse
    {
        $user = $userRepository->find($id);

        if (!$user) {
            return $this->json(['error' => 'Utilisateur non trouvé.'], 404);
        }

        return $this->json(UserDto::fromEntity($user));
    }

    #[Route('/{id}', name: 'update', methods: ['PUT', 'PATCH'])]
    public function update(string $id, Request $request, UserRepository $userRepository, EntityManagerInterface $em): JsonResponse
    {
        $user = $userRepository->find($id);
        if (!$user) {
            return $this->json(['error' => 'Utilisateur non trouvé.'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['username'])) {
            $user->setUsername($data['username']);
        }

        if (isset($data['roles']) && is_array($data['roles'])) {
            $user->setRoles($data['roles']);
        }

        if (isset($data['network'])) {
            $user->setNetwork($data['network']);
        }

        if (isset($data['solBalance'])) {
            $user->setSolBalance($data['solBalance']);
        }

        if (isset($data['ethBalance'])) {
            $user->setEthBalance($data['ethBalance']);
        }

        if (isset($data['workBalance'])) {
            $user->setWorkBalance($data['workBalance']);
        }

        $user->setUpdatedAt(new \DateTimeImmutable());
        $em->flush();

        return $this->json([
            'message' => '✅ Utilisateur mis à jour avec succès.',
            'user' => UserDto::fromEntity($user),
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id, UserRepository $userRepository, EntityManagerInterface $em): JsonResponse
    {
        $user = $userRepository->find($id);
        if (!$user) {
            return $this->json(['error' => 'Utilisateur non trouvé.'], 404);
        }

        $em->remove($user);
        $em->flush();

        return $this->json(['message' => '🗑️ Utilisateur supprimé avec succès.']);
    }
}
