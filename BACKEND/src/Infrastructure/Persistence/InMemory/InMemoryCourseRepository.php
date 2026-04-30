<?php

declare(strict_types=1);

namespace School\Infrastructure\Persistence\InMemory;

use School\Domain\Entity\Course;
use School\Domain\Repository\CourseRepositoryInterface;

class InMemoryCourseRepository extends AbstractInMemoryRepository implements CourseRepositoryInterface
{
    public function save(Course $course): void
    {
        $this->assignId($course);
        $this->items[$course->getId()] = $course;
    }

    public function findById(int $id): ?Course
    {
        return $this->items[$id] ?? null;
    }

    public function delete(Course $course): void
    {
        $id = $course->getId();
        if ($id !== null) {
            unset($this->items[$id]);
        }
    }
}
