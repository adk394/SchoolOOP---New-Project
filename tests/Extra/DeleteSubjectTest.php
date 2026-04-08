<?php

declare(strict_types=1);

namespace School\Tests\Extra;

use PHPUnit\Framework\TestCase;
use School\Application\UseCase\DeleteSubject;
use School\Domain\Entity\Course;
use School\Domain\Entity\Subject;
use School\Infrastructure\Persistence\InMemory\InMemorySubjectRepository;

class DeleteSubjectTest extends TestCase
{
    public function testDeleteSubject(): void
    {
        $repository = new InMemorySubjectRepository();
        $subject = new Subject('Desarrollo Web', new Course('2DAW'));
        $repository->save($subject);

        $useCase = new DeleteSubject($repository);
        $useCase->execute($subject->getId());

        $this->assertNull($repository->findById($subject->getId()));
    }
}
