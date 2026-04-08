<?php

declare(strict_types=1);

namespace School\Tests\Domain;

use PHPUnit\Framework\TestCase;
use School\Domain\Entity\Course;

class CreateCourseTest extends TestCase
{
    public function testCreateCourse(): void
    {
        $course = new Course('2DAW');

        $this->assertSame('2DAW', $course->getName());
    }
}
