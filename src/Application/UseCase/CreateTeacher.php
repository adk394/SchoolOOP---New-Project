<?php

declare(strict_types=1);

namespace School\Application\UseCase;

use School\Domain\Entity\Teacher;
use School\Domain\Repository\TeacherRepositoryInterface;

class CreateTeacher
{
    public function __construct(private readonly TeacherRepositoryInterface $teacherRepository)
    {
    }

    public function execute(string $name, string $email): Teacher
    {
        $teacher = new Teacher($name, $email);
        $this->teacherRepository->save($teacher);

        return $teacher;
    }
}
