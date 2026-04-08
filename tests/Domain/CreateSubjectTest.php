<?php

declare(strict_types=1);

namespace School\Tests\Domain;

use PHPUnit\Framework\TestCase;
use School\Domain\Entity\Course;
use School\Domain\Entity\Subject;

class CreateSubjectTest extends TestCase
{
    public function testCreateSubject(): void
    {
        $course = new Course('2DAW');
        $subject = new Subject('Desarrollo Web', $course);

        $this->assertSame('Desarrollo Web', $subject->getName());
        $this->assertSame('2DAW', $subject->getCourse()->getName());
        $this->assertNull($subject->getTeacher());
    }
}
