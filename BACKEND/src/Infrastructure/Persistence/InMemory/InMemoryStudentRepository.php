<?php

declare(strict_types=1);

namespace School\Infrastructure\Persistence\InMemory;

use ReflectionClass;
use School\Domain\Entity\Student;
use School\Domain\Repository\StudentRepositoryInterface;

class InMemoryStudentRepository implements StudentRepositoryInterface
{
    /** @var array<int, Student> */
    private array $items = [];
    private int $nextId = 1;

    public function save(Student $student): void
    {
        if ($student->getId() === null) {
            $this->forceId($student, $this->nextId++);
        }

        $this->items[$student->getId()] = $student;
    }

    public function findById(int $id): ?Student
    {
        return $this->items[$id] ?? null;
    }

    public function delete(Student $student): void
    {
        if ($student->getId() === null) {
            return;
        }

        unset($this->items[$student->getId()]);
    }

    private function forceId(Student $student, int $id): void
    {
        $reflection = new ReflectionClass($student);
        $property = $reflection->getProperty('id');
        $property->setValue($student, $id);
    }
}
