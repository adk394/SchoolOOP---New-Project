<?php

declare(strict_types=1);

namespace School\Tests\Extra;

use PHPUnit\Framework\TestCase;
use School\Application\UseCase\DeleteTeacher;
use School\Domain\Entity\Teacher;
use School\Infrastructure\Persistence\InMemory\InMemoryTeacherRepository;

class DeleteTeacherTest extends TestCase
{
    public function testDeleteTeacher(): void
    {
        $repository = new InMemoryTeacherRepository();
        $teacher = new Teacher('Pepe', 'pepe@test.com');
        $repository->save($teacher);

        $useCase = new DeleteTeacher($repository);
        $useCase->execute($teacher->getId());

        $this->assertNull($repository->findById($teacher->getId()));
    }
}
