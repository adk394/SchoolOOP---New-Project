<?php

declare(strict_types=1);

namespace School\Infrastructure\Controller;

use InvalidArgumentException;
use School\Infrastructure\AppFactory;

class SchoolController
{
    public function __construct(private readonly AppFactory $factory)
    {
    }

    public function handle(array $post): array
    {
        $action = $post['action'] ?? '';

        try {
            return match ($action) {
                'create_student' => $this->createStudent($post),
                'delete_student' => $this->deleteStudent($post),
                'create_course' => $this->createCourse($post),
                'delete_course' => $this->deleteCourse($post),
                'create_subject' => $this->createSubject($post),
                'delete_subject' => $this->deleteSubject($post),
                'create_teacher' => $this->createTeacher($post),
                'delete_teacher' => $this->deleteTeacher($post),
                'enroll_student' => $this->enrollStudent($post),
                'assign_teacher' => $this->assignTeacher($post),
                default => ['error' => 'Accion no valida'],
            };
        } catch (InvalidArgumentException $e) {
            return ['error' => $e->getMessage()];
        } catch (\Throwable $e) {
            return ['error' => 'Error interno: ' . $e->getMessage()];
        }
    }

    private function createStudent(array $post): array
    {
        $student = $this->factory->createStudentUseCase()->execute(
            trim((string) ($post['name'] ?? '')),
            trim((string) ($post['email'] ?? ''))
        );

        return ['success' => 'Student creado con ID ' . $student->getId()];
    }

    private function deleteStudent(array $post): array
    {
        $this->factory->deleteStudentUseCase()->execute((int) ($post['student_id'] ?? 0));

        return ['success' => 'Student eliminado correctamente'];
    }

    private function createCourse(array $post): array
    {
        $course = $this->factory->createCourseUseCase()->execute(
            trim((string) ($post['name'] ?? ''))
        );

        return ['success' => 'Course creado con ID ' . $course->getId()];
    }

    private function deleteCourse(array $post): array
    {
        $this->factory->deleteCourseUseCase()->execute((int) ($post['course_id'] ?? 0));

        return ['success' => 'Course eliminado correctamente'];
    }

    private function createSubject(array $post): array
    {
        $subject = $this->factory->createSubjectUseCase()->execute(
            trim((string) ($post['name'] ?? '')),
            (int) ($post['course_id'] ?? 0)
        );

        return ['success' => 'Subject creado con ID ' . $subject->getId()];
    }

    private function deleteSubject(array $post): array
    {
        $this->factory->deleteSubjectUseCase()->execute((int) ($post['subject_id'] ?? 0));

        return ['success' => 'Subject eliminado correctamente'];
    }

    private function createTeacher(array $post): array
    {
        $teacher = $this->factory->createTeacherUseCase()->execute(
            trim((string) ($post['name'] ?? '')),
            trim((string) ($post['email'] ?? ''))
        );

        return ['success' => 'Teacher creado con ID ' . $teacher->getId()];
    }

    private function deleteTeacher(array $post): array
    {
        $this->factory->deleteTeacherUseCase()->execute((int) ($post['teacher_id'] ?? 0));

        return ['success' => 'Teacher eliminado correctamente'];
    }

    private function enrollStudent(array $post): array
    {
        $this->factory->enrollStudentUseCase()->execute(
            (int) ($post['student_id'] ?? 0),
            (int) ($post['course_id'] ?? 0)
        );

        return ['success' => 'Student matriculado correctamente'];
    }

    private function assignTeacher(array $post): array
    {
        $this->factory->assignTeacherToSubjectUseCase()->execute(
            (int) ($post['teacher_id'] ?? 0),
            (int) ($post['subject_id'] ?? 0)
        );

        return ['success' => 'Teacher asignado correctamente'];
    }
}
