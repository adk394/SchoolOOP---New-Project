<?php

declare(strict_types=1);

namespace School\Tests\Domain;

use PHPUnit\Framework\TestCase;
use School\Domain\Entity\Student;

class CreateStudentTest extends TestCase
{
    public function testCreateStudent(): void
    {
        $student = new Student('Ana', 'ana@test.com');

        $this->assertSame('Ana', $student->getName());
        $this->assertSame('ana@test.com', $student->getEmail());
        $this->assertNull($student->getCourse());
    }
}
