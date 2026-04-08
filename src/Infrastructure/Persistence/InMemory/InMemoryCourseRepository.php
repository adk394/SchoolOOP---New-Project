<?php

declare(strict_types=1);

namespace School\Infrastructure\Persistence\InMemory;

use ReflectionClass;
use School\Domain\Entity\Course;
use School\Domain\Repository\CourseRepositoryInterface;

class InMemoryCourseRepository implements CourseRepositoryInterface
{
    /** @var array<int, Course> */
    private array $items = [];
    private int $nextId = 1;

    public function save(Course $course): void
    {
        if ($course->getId() === null) {
            $this->forceId($course, $this->nextId++);
        }

        $this->items[$course->getId()] = $course;
    }

    public function findById(int $id): ?Course
    {
        return $this->items[$id] ?? null;
    }

    public function delete(Course $course): void
    {
        if ($course->getId() === null) {
            return;
        }

        unset($this->items[$course->getId()]);
    }

    private function forceId(Course $course, int $id): void
    {
        $reflection = new ReflectionClass($course);
        $property = $reflection->getProperty('id');
        $property->setValue($course, $id);
    }
}
