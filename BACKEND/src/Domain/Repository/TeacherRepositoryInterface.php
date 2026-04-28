<?php

declare(strict_types=1);

namespace School\Domain\Repository;

use School\Domain\Entity\Teacher;

interface TeacherRepositoryInterface
{
    public function save(Teacher $teacher): void;

    public function findById(int $id): ?Teacher;

    public function delete(Teacher $teacher): void;
}
