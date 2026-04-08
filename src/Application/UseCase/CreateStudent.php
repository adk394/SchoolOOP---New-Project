<?php

declare(strict_types=1);

namespace School\Application\UseCase;

use School\Domain\Entity\Student;
use School\Domain\Repository\StudentRepositoryInterface;

class CreateStudent
{
    public function __construct(private readonly StudentRepositoryInterface $studentRepository)
    {
    }

    public function execute(string $name, string $email): Student
    {
        $student = new Student($name, $email);
        $this->studentRepository->save($student);

        return $student;
    }
}
