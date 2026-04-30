<?php

declare(strict_types=1);

namespace School\Infrastructure\Persistence\InMemory;

use School\Domain\Entity\Student;
use School\Domain\Repository\StudentRepositoryInterface;

class InMemoryStudentRepository extends AbstractInMemoryRepository implements StudentRepositoryInterface
{
    public function save(Student $student): void
    {
        $this->assignId($student);
        $this->items[$student->getId()] = $student;
    }

    public function findById(int $id): ?Student
    {
        return $this->items[$id] ?? null;
    }

    public function delete(Student $student): void
    {
        $id = $student->getId();
        if ($id !== null) {
            unset($this->items[$id]);
        }
    }
}
