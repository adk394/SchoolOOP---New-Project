<?php

declare(strict_types=1);

namespace School\Application\UseCase;

use InvalidArgumentException;
use School\Domain\Repository\SubjectRepositoryInterface;
use School\Domain\Repository\TeacherRepositoryInterface;

class AssignTeacherToSubject
{
    public function __construct(
        private readonly SubjectRepositoryInterface $subjectRepository,
        private readonly TeacherRepositoryInterface $teacherRepository,
    ) {}

    public function execute(int $teacherId, int $subjectId): void
    {
        $teacher = $this->teacherRepository->findById($teacherId);
        if ($teacher === null) {
            throw new InvalidArgumentException('Teacher not found');
        }

        $subject = $this->subjectRepository->findById($subjectId);
        if ($subject === null) {
            throw new InvalidArgumentException('Subject not found');
        }

        $subject->assignTeacher($teacher);
        $this->subjectRepository->save($subject);
    }
}
