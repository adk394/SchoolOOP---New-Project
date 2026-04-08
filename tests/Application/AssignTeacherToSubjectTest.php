<?php

declare(strict_types=1);

namespace School\Tests\Application;

use PHPUnit\Framework\TestCase;
use School\Application\UseCase\AssignTeacherToSubject;
use School\Domain\Entity\Course;
use School\Domain\Entity\Subject;
use School\Domain\Entity\Teacher;
use School\Infrastructure\Persistence\InMemory\InMemorySubjectRepository;
use School\Infrastructure\Persistence\InMemory\InMemoryTeacherRepository;

class AssignTeacherToSubjectTest extends TestCase
{
    public function testAssignTeacherToSubject(): void
    {
        $teacherRepository = new InMemoryTeacherRepository();
        $subjectRepository = new InMemorySubjectRepository();

        $teacher = new Teacher('Pepe', 'pepe@test.com');
        $subject = new Subject('Desarrollo Web', new Course('2DAW'));

        $teacherRepository->save($teacher);
        $subjectRepository->save($subject);

        $useCase = new AssignTeacherToSubject($subjectRepository, $teacherRepository);
        $useCase->execute($teacher->getId(), $subject->getId());

        $savedSubject = $subjectRepository->findById($subject->getId());

        $this->assertNotNull($savedSubject);
        $this->assertNotNull($savedSubject->getTeacher());
        $this->assertSame('Pepe', $savedSubject->getTeacher()->getName());
    }
}
