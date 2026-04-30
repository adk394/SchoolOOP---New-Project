<?php

declare(strict_types=1);

namespace School\Infrastructure\Persistence\InMemory;

use School\Domain\Entity\Subject;
use School\Domain\Repository\SubjectRepositoryInterface;

class InMemorySubjectRepository extends AbstractInMemoryRepository implements SubjectRepositoryInterface
{
    public function save(Subject $subject): void
    {
        $this->assignId($subject);
        $this->items[$subject->getId()] = $subject;
    }

    public function findById(int $id): ?Subject
    {
        return $this->items[$id] ?? null;
    }

    public function delete(Subject $subject): void
    {
        $id = $subject->getId();
        if ($id !== null) {
            unset($this->items[$id]);
        }
    }
}
