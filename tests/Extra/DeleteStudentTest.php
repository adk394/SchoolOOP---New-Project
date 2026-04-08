<?php

declare(strict_types=1);

namespace School\Tests\Extra;

use PHPUnit\Framework\TestCase;
use School\Application\UseCase\DeleteStudent;
use School\Domain\Entity\Student;
use School\Infrastructure\Persistence\InMemory\InMemoryStudentRepository;

class DeleteStudentTest extends TestCase
{
    public function testDeleteStudent(): void
    {
        $repository = new InMemoryStudentRepository();
        $student = new Student('Ana', 'ana@test.com');
        $repository->save($student);

        $useCase = new DeleteStudent($repository);
        $useCase->execute($student->getId());

        $this->assertNull($repository->findById($student->getId()));
    }
}
