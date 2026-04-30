<?php

declare(strict_types=1);

namespace School\Domain\Repository;

use School\Domain\Entity\Course;

interface CourseRepositoryInterface
{
    public function save(Course $course): void;

    public function findById(int $id): ?Course;

    public function delete(Course $course): void;
}
