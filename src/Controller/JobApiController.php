<?php

namespace App\Controller;

use App\Entity\Job;
use App\Repository\UserRepository;
use App\Repository\JobRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/jobs', name: 'api_jobs_')]
class JobApiController extends AbstractController
{
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, UserRepository $userRepo): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['recruiterId'])) {
            return $this->json(['error' => 'Missing recruiterId'], 400);
        }

        $recruiter = $userRepo->find($data['recruiterId']);
        if (!$recruiter) {
            return $this->json(['error' => 'Recruiter not found'], 404);
        }

        $job = new Job();
        $job->setRecruiter($recruiter);
        $job->setTitle($data['title'] ?? 'Sans titre');
        $job->setDescription($data['description'] ?? '');
        $job->setBudget($data['budget'] ?? '0');
        $job->setCurrency($data['currency'] ?? 'WORK');
        $job->setStatus($data['status'] ?? 'open');
        $job->setCategory($data['category'] ?? null);
        $job->setDuration($data['duration'] ?? null);
        $job->setSkills($data['skills'] ?? []);

        $em->persist($job);
        $em->flush();

        return $this->json([
            'id' => $job->getId(),
            'title' => $job->getTitle(),
            'description' => $job->getDescription(),
            'budget' => $job->getBudget(),
            'currency' => $job->getCurrency(),
            'status' => $job->getStatus(),
            'category' => $job->getCategory(),
            'duration' => $job->getDuration(),
            'skills' => $job->getSkills(),
            'createdAt' => $job->getCreatedAt()->format('Y-m-d H:i:s'),
            'recruiterId' => $recruiter->getId(),
            'recruiterUsername' => $recruiter->getUsername(),
        ], 201);
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(JobRepository $jobRepo): JsonResponse
    {
        $jobs = $jobRepo->findAll();

        $data = array_map(fn(Job $job) => [
            'id' => $job->getId(),
            'title' => $job->getTitle(),
            'description' => $job->getDescription(),
            'budget' => $job->getBudget(),
            'currency' => $job->getCurrency(),
            'status' => $job->getStatus(),
            'category' => $job->getCategory(),
            'duration' => $job->getDuration(),
            'skills' => $job->getSkills(),
            'createdAt' => $job->getCreatedAt()->format('Y-m-d H:i:s'),
            'recruiterId' => $job->getRecruiter()?->getId(),
            'recruiterUsername' => $job->getRecruiter()?->getUsername(),
        ], $jobs);

        return $this->json($data);
    }
}
