<?php

declare(strict_types=1);

namespace School\Infrastructure\Persistence\InMemory;

abstract class AbstractInMemoryRepository
{
    /**
     * @var array<int, object>
     */
    protected array $items = [];

    protected int $nextId = 1;

    protected function assignId(object $entity): void
    {
        $reflection = new \ReflectionClass($entity);
        if (!$reflection->hasProperty('id')) {
            throw new \RuntimeException('Entity does not have an id property.');
        }

        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $currentId = $property->getValue($entity);

        if ($currentId !== null) {
            return;
        }

        $property->setValue($entity, $this->nextId);
        $this->nextId++;
    }
}
