<?php

declare(strict_types=1);

namespace School\Application\UseCase;

use School\Domain\Entity\Course;
use School\Domain\Repository\CourseRepositoryInterface;

class CreateCourse
{
    public function __construct(private readonly CourseRepositoryInterface $courseRepository) {}

    public function execute(string $name): Course
    {
        $course = new Course($name);
        $this->courseRepository->save($course);

        return $course;
    }
}
