<?php

declare(strict_types=1);

namespace School\Application\UseCase;

use InvalidArgumentException;
use School\Domain\Entity\Subject;
use School\Domain\Repository\CourseRepositoryInterface;
use School\Domain\Repository\SubjectRepositoryInterface;

class CreateSubject
{
    public function __construct(
        private readonly SubjectRepositoryInterface $subjectRepository,
        private readonly CourseRepositoryInterface $courseRepository,
    ) {
    }

    public function execute(string $name, int $courseId): Subject
    {
        $course = $this->courseRepository->findById($courseId);
        if ($course === null) {
            throw new InvalidArgumentException('Course not found');
        }

        $subject = new Subject($name, $course);
        $this->subjectRepository->save($subject);

        return $subject;
    }
}
