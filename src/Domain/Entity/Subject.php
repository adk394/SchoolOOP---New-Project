<?php

declare(strict_types=1);

namespace School\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'subjects')]
class Subject
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 140)]
    private string $name;

    #[ORM\ManyToOne(targetEntity: Course::class)]
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'id', nullable: false)]
    private Course $course;

    #[ORM\ManyToOne(targetEntity: Teacher::class)]
    #[ORM\JoinColumn(name: 'teacher_id', referencedColumnName: 'id', nullable: true)]
    private ?Teacher $teacher = null;

    public function __construct(string $name, Course $course)
    {
        $this->name = $name;
        $this->course = $course;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCourse(): Course
    {
        return $this->course;
    }

    public function assignTeacher(Teacher $teacher): void
    {
        $this->teacher = $teacher;
    }

    public function getTeacher(): ?Teacher
    {
        return $this->teacher;
    }
}
