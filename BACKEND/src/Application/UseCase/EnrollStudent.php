<?php

declare(strict_types=1);

namespace School\Application\UseCase;

use InvalidArgumentException;
use School\Domain\Repository\CourseRepositoryInterface;
use School\Domain\Repository\StudentRepositoryInterface;

class EnrollStudent
{
    public function __construct(
        private readonly StudentRepositoryInterface $studentRepository,
        private readonly CourseRepositoryInterface $courseRepository,
    ) {}

    public function execute(int $studentId, int $courseId): void
    {
        $student = $this->studentRepository->findById($studentId);
        if ($student === null) {
            throw new InvalidArgumentException('Student not found');
        }

        $course = $this->courseRepository->findById($courseId);
        if ($course === null) {
            throw new InvalidArgumentException('Course not found');
        }

        $student->enrollToCourse($course);
        $this->studentRepository->save($student);
    }
}
