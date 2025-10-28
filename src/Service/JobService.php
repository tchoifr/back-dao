<?php

namespace App\Service;

use App\Dto\CreateJobDto;
use App\Entity\Job;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class JobService
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserRepository $userRepo
    ) {}

    public function createJobFromDto(CreateJobDto $dto): Job
    {
        $recruiter = $this->userRepo->find($dto->recruiterId);
        if (!$recruiter) {
            throw new \RuntimeException('Recruiter not found');
        }

        $job = new Job();
        $job
            ->setRecruiter($recruiter)
            ->setTitle($dto->title)
            ->setDescription($dto->description ?? '')
            ->setCategory($dto->category)
            ->setDuration($dto->duration)
            ->setSkills($dto->skills)
            ->setBudget($dto->budget ?? '0')
            ->setCurrency($dto->currency ?? 'WORK')
            ->setStatus($dto->status ?? 'open');

        $this->em->persist($job);
        $this->em->flush();

        return $job;
    }
}
