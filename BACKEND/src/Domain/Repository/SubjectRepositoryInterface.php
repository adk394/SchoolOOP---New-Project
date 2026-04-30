<?php

declare(strict_types=1);

namespace School\Domain\Repository;

use School\Domain\Entity\Subject;

interface SubjectRepositoryInterface
{
    public function save(Subject $subject): void;

    public function findById(int $id): ?Subject;

    public function delete(Subject $subject): void;
}
