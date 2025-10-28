<?php

namespace App\Service;

use App\Entity\User;
use App\Dto\JobDto;
use App\Repository\JobRepository;

class JobService
{
    public function __construct(private JobRepository $jobRepository) {}

    public function getJobsForUser(User $user): array
    {
        $roles = $user->getRoles();
        $isAdmin = in_array('admin', $roles, true);
        $isDao = in_array('dao', $roles, true);
        $isFreelance = in_array('freelance', $roles, true);
        $isEmployer = in_array('employer', $roles, true);

        if ($isAdmin || $isDao) {
            $jobs = $this->jobRepository->findAll();
            return array_map(fn($j) => JobDto::forAdmin($j)->toArray(), $jobs);
        }

        if ($isEmployer) {
            $jobs = $this->jobRepository->findBy(['recruiter' => $user]);
            return array_map(fn($j) => JobDto::forEmployer($j)->toArray(), $jobs);
        }

        if ($isFreelance) {
            $jobs = $this->jobRepository->findBy(['status' => 'open']);
            return array_map(fn($j) => JobDto::forFreelance($j)->toArray(), $jobs);
        }

        return [];
    }
}
