<?php

namespace App\Controller;

use App\Dto\CreateJobDto;
use App\Service\JobService;
use App\Dto\JobDto;
use App\Repository\UserRepository;
use App\Entity\Job;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/jobs', name: 'api_jobs_')]
class JobApiController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private JobService $jobService
    ) {}

    // 🟢 CRÉATION D’UN JOB
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, UserRepository $userRepo, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $dto = CreateJobDto::fromArray($data);
        $errors = $validator->validate($dto);

        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['errors' => $messages], 400);
        }

        $recruiter = $userRepo->find($dto->recruiterId);
        if (!$recruiter) {
            return $this->json(['error' => 'Recruiter not found'], 404);
        }

        $job = new Job();
        $job->setRecruiter($recruiter);
        $job->setTitle($dto->title);
        $job->setDescription($dto->description ?? '');
        $job->setBudget($dto->budget ?? '0');
        $job->setCurrency($dto->currency ?? 'WORK');
        $job->setStatus($dto->status ?? 'open');
        $job->setCategory($dto->category);
        $job->setDuration($dto->duration);
        $job->setSkills($dto->skills ?? []);

        $this->em->persist($job);
        $this->em->flush();

        return $this->json(JobDto::forEmployer($job)->toArray(), 201);
    }

    // 🔵 LISTE DES JOBS PAR UTILISATEUR
    #[Route('', name: 'get_jobs_by_user', methods: ['GET'])]
    public function list(Request $request, UserRepository $userRepo): JsonResponse
    {
        $userId = $request->query->get('userId');
        if (!$userId) {
            return $this->json(['error' => 'Missing userId parameter'], 400);
        }

        $user = $userRepo->find($userId);
        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }

        $jobs = $this->jobService->getJobsForUser($user);
        return $this->json($jobs);
    }

    // 🟣 MISE À JOUR DU STATUT OU D’AUTRES CHAMPS
    #[Route('/{id}', name: 'update', methods: ['PATCH'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $job = $this->em->getRepository(Job::class)->find($id);
        if (!$job) {
            return $this->json(['error' => 'Job not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['status'])) $job->setStatus($data['status']);
        if (isset($data['title'])) $job->setTitle($data['title']);
        if (isset($data['description'])) $job->setDescription($data['description']);
        if (isset($data['budget'])) $job->setBudget($data['budget']);
        if (isset($data['currency'])) $job->setCurrency($data['currency']);
        if (isset($data['category'])) $job->setCategory($data['category']);
        if (isset($data['duration'])) $job->setDuration($data['duration']);
        if (isset($data['skills'])) $job->setSkills($data['skills']);

        $this->em->flush();

        return $this->json([
            'id' => $job->getId(),
            'status' => $job->getStatus(),
            'title' => $job->getTitle(),
        ]);
    }

    // 🔴 SUPPRESSION D’UN JOB
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $job = $this->em->getRepository(Job::class)->find($id);
        if (!$job) {
            return $this->json(['error' => 'Job not found'], 404);
        }

        $this->em->remove($job);
        $this->em->flush();

        return $this->json(['message' => 'Job deleted successfully']);
    }
}
