<?php

declare(strict_types=1);

namespace School\Application\UseCase;

use InvalidArgumentException;
use School\Domain\Repository\CourseRepositoryInterface;

class DeleteCourse
{
    public function __construct(private readonly CourseRepositoryInterface $courseRepository) {}

    public function execute(int $courseId): void
    {
        $course = $this->courseRepository->findById($courseId);
        if ($course === null) {
            throw new InvalidArgumentException('Course not found');
        }

        $this->courseRepository->delete($course);
    }
}
