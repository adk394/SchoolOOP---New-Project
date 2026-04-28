<?php

declare(strict_types=1);

namespace School\Tests\Domain;

use PHPUnit\Framework\TestCase;
use School\Domain\Entity\Teacher;

class CreateTeacherTest extends TestCase
{
    public function testCreateTeacher(): void
    {
        $teacher = new Teacher('Pepe', 'pepe@test.com');

        $this->assertSame('Pepe', $teacher->getName());
        $this->assertSame('pepe@test.com', $teacher->getEmail());
    }
}
