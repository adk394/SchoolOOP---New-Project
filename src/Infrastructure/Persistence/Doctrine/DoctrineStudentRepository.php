<?php

declare(strict_types=1);

namespace School\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use School\Domain\Entity\Student;
use School\Domain\Repository\StudentRepositoryInterface;

class DoctrineStudentRepository implements StudentRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function save(Student $student): void
    {
        $this->entityManager->persist($student);
        $this->entityManager->flush();
    }

    public function findById(int $id): ?Student
    {
        return $this->entityManager->find(Student::class, $id);
    }

    public function delete(Student $student): void
    {
        $this->entityManager->remove($student);
        $this->entityManager->flush();
    }
}
