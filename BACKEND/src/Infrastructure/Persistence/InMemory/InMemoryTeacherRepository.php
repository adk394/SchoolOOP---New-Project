<?php

declare(strict_types=1);

namespace School\Infrastructure\Persistence\InMemory;

use School\Domain\Entity\Teacher;
use School\Domain\Repository\TeacherRepositoryInterface;

class InMemoryTeacherRepository extends AbstractInMemoryRepository implements TeacherRepositoryInterface
{
    public function save(Teacher $teacher): void
    {
        $this->assignId($teacher);
        $this->items[$teacher->getId()] = $teacher;
    }

    public function findById(int $id): ?Teacher
    {
        return $this->items[$id] ?? null;
    }

    public function delete(Teacher $teacher): void
    {
        $id = $teacher->getId();
        if ($id !== null) {
            unset($this->items[$id]);
        }
    }
}
