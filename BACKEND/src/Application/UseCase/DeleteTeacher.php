<?php

declare(strict_types=1);

namespace School\Application\UseCase;

use InvalidArgumentException;
use School\Domain\Repository\TeacherRepositoryInterface;

class DeleteTeacher
{
    public function __construct(private readonly TeacherRepositoryInterface $teacherRepository) {}

    public function execute(int $teacherId): void
    {
        $teacher = $this->teacherRepository->findById($teacherId);
        if ($teacher === null) {
            throw new InvalidArgumentException('Teacher not found');
        }

        $this->teacherRepository->delete($teacher);
    }
}
