<?php

declare(strict_types=1);

namespace School\Domain\Repository;

use School\Domain\Entity\Student;

interface StudentRepositoryInterface
{
    public function save(Student $student): void;

    public function findById(int $id): ?Student;

    public function delete(Student $student): void;
}
