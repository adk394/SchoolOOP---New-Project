<?php

declare(strict_types=1);

namespace School\Infrastructure\Http\Controller;

use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use School\Application\UseCase\CreateSubject;
use School\Application\UseCase\DeleteSubject;
use School\Domain\Entity\Course;
use School\Domain\Entity\Subject;
use School\Domain\Entity\Teacher;
use School\Infrastructure\Http\ApiRequest;
use School\Infrastructure\Http\ApiResponse;
use School\Infrastructure\Persistence\Doctrine\DoctrineCourseRepository;
use School\Infrastructure\Persistence\Doctrine\DoctrineSubjectRepository;

final class SubjectsApiController
{
    private DoctrineSubjectRepository $subjectRepository;
    private DoctrineCourseRepository $courseRepository;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        $this->subjectRepository = new DoctrineSubjectRepository($entityManager);
        $this->courseRepository = new DoctrineCourseRepository($entityManager);
    }

    public function index(): void
    {
        $items = $this->entityManager->getRepository(Subject::class)->findAll();
        $result = array_map(fn (Subject $subject): array => $this->toArray($subject), $items);

        ApiResponse::json(200, ['data' => $result]);
    }

    public function show(int $id): void
    {
        $subject = $this->subjectRepository->findById($id);
        if ($subject === null) {
            ApiResponse::json(404, ['error' => 'Subject not found']);
            return;
        }

        ApiResponse::json(200, ['data' => $this->toArray($subject)]);
    }

    public function create(ApiRequest $request): void
    {
        $body = $request->getBody();
        $name = trim((string) ($body['name'] ?? ''));
        $courseId = (int) ($body['course_id'] ?? 0);

        if ($name === '' || $courseId <= 0) {
            ApiResponse::json(400, ['error' => 'name and course_id are required']);
            return;
        }

        try {
            $subject = (new CreateSubject($this->subjectRepository, $this->courseRepository))->execute($name, $courseId);
            ApiResponse::json(201, ['data' => $this->toArray($subject)]);
        } catch (InvalidArgumentException $e) {
            ApiResponse::json(404, ['error' => $e->getMessage()]);
        } catch (\Throwable $e) {
            ApiResponse::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function update(int $id, ApiRequest $request): void
    {
        $subject = $this->subjectRepository->findById($id);
        if ($subject === null) {
            ApiResponse::json(404, ['error' => 'Subject not found']);
            return;
        }

        $body = $request->getBody();

        try {
            if (array_key_exists('name', $body)) {
                $name = trim((string) $body['name']);
                if ($name === '') {
                    ApiResponse::json(400, ['error' => 'name cannot be empty']);
                    return;
                }
                $subject->updateName($name);
            }

            if (array_key_exists('course_id', $body)) {
                $courseId = (int) $body['course_id'];
                if ($courseId <= 0) {
                    ApiResponse::json(400, ['error' => 'course_id must be > 0']);
                    return;
                }

                $course = $this->courseRepository->findById($courseId);
                if ($course === null) {
                    ApiResponse::json(404, ['error' => 'Course not found']);
                    return;
                }

                $subject->updateCourse($course);
            }

            if (array_key_exists('teacher_id', $body)) {
                $teacherId = $body['teacher_id'];

                if ($teacherId === null) {
                    $subject->unassignTeacher();
                } else {
                    $teacher = $this->entityManager->find(Teacher::class, (int) $teacherId);
                    if ($teacher === null) {
                        ApiResponse::json(404, ['error' => 'Teacher not found']);
                        return;
                    }
                    $subject->assignTeacher($teacher);
                }
            }

            $this->subjectRepository->save($subject);
            ApiResponse::json(200, ['data' => $this->toArray($subject)]);
        } catch (\Throwable $e) {
            ApiResponse::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function delete(int $id): void
    {
        try {
            (new DeleteSubject($this->subjectRepository))->execute($id);
            ApiResponse::noContent();
        } catch (InvalidArgumentException $e) {
            ApiResponse::json(404, ['error' => $e->getMessage()]);
        } catch (\Throwable $e) {
            ApiResponse::json(500, ['error' => $e->getMessage()]);
        }
    }

    private function toArray(Subject $subject): array
    {
        return [
            'id' => $subject->getId(),
            'name' => $subject->getName(),
            'course_id' => $subject->getCourse()->getId(),
            'teacher_id' => $subject->getTeacher()?->getId(),
        ];
    }
}
