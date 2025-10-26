<?php

namespace App\Controller;

use App\Dto\CreateUserDto;
use App\Dto\UserDto;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Uid\Uuid;

#[Route('/api/users', name: 'api_users_')]
class UserApiController extends AbstractController
{
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        if ($data === null) {
            return $this->json(['error' => 'Invalid JSON'], 400);
        }

        $dto = new CreateUserDto();
        $dto->id = $data['id'] ?? null;
        $dto->walletAddress = $data['walletAddress'] ?? '';
        $dto->username = $data['username'] ?? null;
        $dto->role = $data['role'] ?? '';
        $dto->userToken = $data['userToken'] ?? null;
        $dto->network = $data['network'] ?? '';
        $dto->solBalance = $data['solBalance'] ?? null;
        $dto->ethBalance = $data['ethBalance'] ?? null;
        $dto->workBalance = $data['workBalance'] ?? null;
        $dto->createdAt = $data['createdAt'] ?? null;
        $dto->updatedAt = $data['updatedAt'] ?? null;

        $errors = $validator->validate($dto);
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['errors' => $messages], 400);
        }

        $user = new User();

        if ($dto->id) {
            $reflection = new \ReflectionProperty(User::class, 'id');
            $reflection->setAccessible(true);
            $reflection->setValue($user, Uuid::fromString($dto->id));
        }

        $user->setWalletAddress($dto->walletAddress);
        $user->setUsername($dto->username);
        $user->setRole($dto->role);
        $user->setUserToken($dto->userToken);
        $user->setNetwork($dto->network);
        $user->setSolBalance($dto->solBalance);
        $user->setEthBalance($dto->ethBalance);
        $user->setWorkBalance($dto->workBalance);

        if ($dto->createdAt) {
            $user->setCreatedAt(new \DateTimeImmutable($dto->createdAt));
        }

        if ($dto->updatedAt) {
            $user->setUpdatedAt(new \DateTimeImmutable($dto->updatedAt));
        }

        $em->persist($user);
        $em->flush();

        // ✅ maintenant fonctionne directement
        return $this->json(UserDto::fromEntity($user), 201);
    }

    #[Route('', name: 'get_all', methods: ['GET'])]
    public function getAll(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();
        $dtos = array_map(fn($u) => UserDto::fromEntity($u), $users);

        // ✅ fonctionne grâce à jsonSerialize()
        return $this->json($dtos);
    }

    #[Route('/{id}', name: 'get_one', methods: ['GET'])]
    public function getOne(string $id, UserRepository $userRepository): JsonResponse
    {
        $user = $userRepository->find($id);

        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }

        return $this->json(UserDto::fromEntity($user));
    }
}
