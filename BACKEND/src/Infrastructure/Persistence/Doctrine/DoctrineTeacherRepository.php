<?php

declare(strict_types=1);

namespace School\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use School\Domain\Entity\Teacher;
use School\Domain\Repository\TeacherRepositoryInterface;

class DoctrineTeacherRepository implements TeacherRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function save(Teacher $teacher): void
    {
        $this->entityManager->persist($teacher);
        $this->entityManager->flush();
    }

    public function findById(int $id): ?Teacher
    {
        return $this->entityManager->find(Teacher::class, $id);
    }

    public function delete(Teacher $teacher): void
    {
        $this->entityManager->remove($teacher);
        $this->entityManager->flush();
    }
}
