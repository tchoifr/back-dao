<?php

namespace App\Controller;

use App\Dto\CreateJobDto;
use App\Dto\JobDto;
use App\Service\JobService;
use App\Repository\JobRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/jobs', name: 'api_jobs_')]
class JobApiController extends AbstractController
{
    public function __construct(
        private readonly JobService $jobService
    ) {}

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->json(['error' => 'Invalid JSON'], 400);
        }

        $dto = CreateJobDto::fromArray($data);

        $errors = $validator->validate($dto);
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['errors' => $messages], 400);
        }

        try {
            $job = $this->jobService->createJobFromDto($dto);
        } catch (\RuntimeException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }

        return $this->json(JobDto::fromEntity($job), 201);
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(JobRepository $jobRepo): JsonResponse
    {
        $jobs = $jobRepo->findAll();
        $dtos = array_map(fn($job) => JobDto::fromEntity($job), $jobs);

        return $this->json($dtos);
    }
}
