<?php

declare(strict_types=1);

namespace School\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use School\Domain\Entity\Subject;
use School\Domain\Repository\SubjectRepositoryInterface;

class DoctrineSubjectRepository implements SubjectRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function save(Subject $subject): void
    {
        $this->entityManager->persist($subject);
        $this->entityManager->flush();
    }

    public function findById(int $id): ?Subject
    {
        return $this->entityManager->find(Subject::class, $id);
    }

    public function delete(Subject $subject): void
    {
        $this->entityManager->remove($subject);
        $this->entityManager->flush();
    }
}
