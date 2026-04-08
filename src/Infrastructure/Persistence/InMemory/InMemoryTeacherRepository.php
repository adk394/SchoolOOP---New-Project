<?php

declare(strict_types=1);

namespace School\Infrastructure\Persistence\InMemory;

use ReflectionClass;
use School\Domain\Entity\Teacher;
use School\Domain\Repository\TeacherRepositoryInterface;

class InMemoryTeacherRepository implements TeacherRepositoryInterface
{
    /** @var array<int, Teacher> */
    private array $items = [];
    private int $nextId = 1;

    public function save(Teacher $teacher): void
    {
        if ($teacher->getId() === null) {
            $this->forceId($teacher, $this->nextId++);
        }

        $this->items[$teacher->getId()] = $teacher;
    }

    public function findById(int $id): ?Teacher
    {
        return $this->items[$id] ?? null;
    }

    public function delete(Teacher $teacher): void
    {
        if ($teacher->getId() === null) {
            return;
        }

        unset($this->items[$teacher->getId()]);
    }

    private function forceId(Teacher $teacher, int $id): void
    {
        $reflection = new ReflectionClass($teacher);
        $property = $reflection->getProperty('id');
        $property->setValue($teacher, $id);
    }
}
