<?php

declare(strict_types=1);

namespace School\Tests\Extra;

use PHPUnit\Framework\TestCase;
use School\Application\UseCase\DeleteCourse;
use School\Domain\Entity\Course;
use School\Infrastructure\Persistence\InMemory\InMemoryCourseRepository;

class DeleteCourseTest extends TestCase
{
    public function testDeleteCourse(): void
    {
        $repository = new InMemoryCourseRepository();
        $course = new Course('2DAW');
        $repository->save($course);

        $useCase = new DeleteCourse($repository);
        $useCase->execute($course->getId());

        $this->assertNull($repository->findById($course->getId()));
    }
}
