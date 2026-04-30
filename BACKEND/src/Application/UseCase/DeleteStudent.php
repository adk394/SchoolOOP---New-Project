<?php

declare(strict_types=1);

namespace School\Application\UseCase;

use InvalidArgumentException;
use School\Domain\Repository\StudentRepositoryInterface;

class DeleteStudent
{
    public function __construct(private readonly StudentRepositoryInterface $studentRepository) {}

    public function execute(int $studentId): void
    {
        $student = $this->studentRepository->findById($studentId);
        if ($student === null) {
            throw new InvalidArgumentException('Student not found');
        }

        $this->studentRepository->delete($student);
    }
}
