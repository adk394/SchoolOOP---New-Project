<?php

declare(strict_types=1);

namespace School\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use School\Domain\Entity\Course;
use School\Domain\Repository\CourseRepositoryInterface;

class DoctrineCourseRepository implements CourseRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function save(Course $course): void
    {
        $this->entityManager->persist($course);
        $this->entityManager->flush();
    }

    public function findById(int $id): ?Course
    {
        return $this->entityManager->find(Course::class, $id);
    }

    public function delete(Course $course): void
    {
        $this->entityManager->remove($course);
        $this->entityManager->flush();
    }
}
