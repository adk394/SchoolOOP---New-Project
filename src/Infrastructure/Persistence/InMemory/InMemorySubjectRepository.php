<?php

declare(strict_types=1);

namespace School\Infrastructure\Persistence\InMemory;

use ReflectionClass;
use School\Domain\Entity\Subject;
use School\Domain\Repository\SubjectRepositoryInterface;

class InMemorySubjectRepository implements SubjectRepositoryInterface
{
    /** @var array<int, Subject> */
    private array $items = [];
    private int $nextId = 1;

    public function save(Subject $subject): void
    {
        if ($subject->getId() === null) {
            $this->forceId($subject, $this->nextId++);
        }

        $this->items[$subject->getId()] = $subject;
    }

    public function findById(int $id): ?Subject
    {
        return $this->items[$id] ?? null;
    }

    public function delete(Subject $subject): void
    {
        if ($subject->getId() === null) {
            return;
        }

        unset($this->items[$subject->getId()]);
    }

    private function forceId(Subject $subject, int $id): void
    {
        $reflection = new ReflectionClass($subject);
        $property = $reflection->getProperty('id');
        $property->setValue($subject, $id);
    }
}
