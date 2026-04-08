<?php

declare(strict_types=1);

namespace School\Tests\Application;

use PHPUnit\Framework\TestCase;
use School\Application\UseCase\EnrollStudent;
use School\Domain\Entity\Course;
use School\Domain\Entity\Student;
use School\Infrastructure\Persistence\InMemory\InMemoryCourseRepository;
use School\Infrastructure\Persistence\InMemory\InMemoryStudentRepository;

class EnrollStudentTest extends TestCase
{
    public function testEnrollStudent(): void
    {
        $studentRepository = new InMemoryStudentRepository();
        $courseRepository = new InMemoryCourseRepository();

        $student = new Student('Ana', 'ana@test.com');
        $course = new Course('2DAW');

        $studentRepository->save($student);
        $courseRepository->save($course);

        $useCase = new EnrollStudent($studentRepository, $courseRepository);
        $useCase->execute($student->getId(), $course->getId());

        $savedStudent = $studentRepository->findById($student->getId());

        $this->assertNotNull($savedStudent);
        $this->assertNotNull($savedStudent->getCourse());
        $this->assertSame('2DAW', $savedStudent->getCourse()->getName());
    }
}
