<?php

namespace App\Controller;

use App\Dto\CreateJobDto;
use App\Service\JobService;
use App\Dto\JobDto;
use App\Repository\UserRepository;
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

        $job = new \App\Entity\Job();
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
}
