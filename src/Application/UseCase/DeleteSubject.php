<?php

declare(strict_types=1);

namespace School\Application\UseCase;

use InvalidArgumentException;
use School\Domain\Repository\SubjectRepositoryInterface;

class DeleteSubject
{
    public function __construct(private readonly SubjectRepositoryInterface $subjectRepository)
    {
    }

    public function execute(int $subjectId): void
    {
        $subject = $this->subjectRepository->findById($subjectId);
        if ($subject === null) {
            throw new InvalidArgumentException('Subject not found');
        }

        $this->subjectRepository->delete($subject);
    }
}
