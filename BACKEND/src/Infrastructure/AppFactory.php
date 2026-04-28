<?php

declare(strict_types=1);

namespace School\Infrastructure;

use Doctrine\ORM\EntityManagerInterface;
use School\Application\UseCase\AssignTeacherToSubject;
use School\Application\UseCase\CreateCourse;
use School\Application\UseCase\CreateStudent;
use School\Application\UseCase\CreateSubject;
use School\Application\UseCase\CreateTeacher;
use School\Application\UseCase\DeleteCourse;
use School\Application\UseCase\DeleteStudent;
use School\Application\UseCase\DeleteSubject;
use School\Application\UseCase\DeleteTeacher;
use School\Application\UseCase\EnrollStudent;
use School\Infrastructure\Persistence\Doctrine\DoctrineCourseRepository;
use School\Infrastructure\Persistence\Doctrine\DoctrineStudentRepository;
use School\Infrastructure\Persistence\Doctrine\DoctrineSubjectRepository;
use School\Infrastructure\Persistence\Doctrine\DoctrineTeacherRepository;

class AppFactory
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function createStudentUseCase(): CreateStudent
    {
        return new CreateStudent(new DoctrineStudentRepository($this->entityManager));
    }

    public function deleteStudentUseCase(): DeleteStudent
    {
        return new DeleteStudent(new DoctrineStudentRepository($this->entityManager));
    }

    public function createCourseUseCase(): CreateCourse
    {
        return new CreateCourse(new DoctrineCourseRepository($this->entityManager));
    }

    public function deleteCourseUseCase(): DeleteCourse
    {
        return new DeleteCourse(new DoctrineCourseRepository($this->entityManager));
    }

    public function createSubjectUseCase(): CreateSubject
    {
        return new CreateSubject(
            new DoctrineSubjectRepository($this->entityManager),
            new DoctrineCourseRepository($this->entityManager)
        );
    }

    public function deleteSubjectUseCase(): DeleteSubject
    {
        return new DeleteSubject(new DoctrineSubjectRepository($this->entityManager));
    }

    public function createTeacherUseCase(): CreateTeacher
    {
        return new CreateTeacher(new DoctrineTeacherRepository($this->entityManager));
    }

    public function deleteTeacherUseCase(): DeleteTeacher
    {
        return new DeleteTeacher(new DoctrineTeacherRepository($this->entityManager));
    }

    public function enrollStudentUseCase(): EnrollStudent
    {
        return new EnrollStudent(
            new DoctrineStudentRepository($this->entityManager),
            new DoctrineCourseRepository($this->entityManager)
        );
    }

    public function assignTeacherToSubjectUseCase(): AssignTeacherToSubject
    {
        return new AssignTeacherToSubject(
            new DoctrineSubjectRepository($this->entityManager),
            new DoctrineTeacherRepository($this->entityManager)
        );
    }
}
