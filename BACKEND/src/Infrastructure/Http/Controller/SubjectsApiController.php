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
    private DoctrineSubjectRepository $repo;
    private DoctrineCourseRepository $courseRepo;
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = new DoctrineSubjectRepository($em);
        $this->courseRepo = new DoctrineCourseRepository($em);
    }

    public function index(): void
    {
        $subjects = $this->em->getRepository(Subject::class)->findAll();
        ApiResponse::json(200, ['data' => array_map(fn(Subject $s) => $this->toArray($s), $subjects)]);
    }

    public function show(int $id): void
    {
        $subject = $this->repo->findById($id);
        $subject 
            ? ApiResponse::json(200, ['data' => $this->toArray($subject)]) 
            : ApiResponse::json(404, ['error' => 'not found']);
    }

    public function create(ApiRequest $request): void
    {
        $body = $request->getBody();
        $name = trim($body['name'] ?? '');
        $courseId = (int) ($body['course_id'] ?? 0);

        if (!$name || $courseId <= 0) {
            ApiResponse::json(400, ['error' => 'faltan datos']);
            return;
        }

        try {
            $subject = (new CreateSubject($this->repo, $this->courseRepo))->execute($name, $courseId);

            if (isset($body['teacher_id']) && $body['teacher_id'] !== '' && $body['teacher_id'] !== null) {
                $teacher = $this->em->find(Teacher::class, (int) $body['teacher_id']);
                if ($teacher) {
                    $subject->assignTeacher($teacher);
                    $this->repo->save($subject);
                }
            }

            ApiResponse::json(201, ['data' => $this->toArray($subject)]);
        } catch (InvalidArgumentException $e) {
            ApiResponse::json(404, ['error' => 'curso no encontrado']);
        }
    }

    public function update(int $id, ApiRequest $request): void
    {
        $subject = $this->repo->findById($id);
        if (!$subject) {
            ApiResponse::json(404, ['error' => 'not found']);
            return;
        }

        $body = $request->getBody();

        if (isset($body['name'])) {
            $name = trim($body['name']);
            if (!$name) {
                ApiResponse::json(400, ['error' => 'nombre invalido']);
                return;
            }
            $subject->updateName($name);
        }

        if (isset($body['course_id'])) {
            $courseId = (int) $body['course_id'];
            if ($courseId <= 0) {
                ApiResponse::json(400, ['error' => 'course_id invalido']);
                return;
            }
            $course = $this->courseRepo->findById($courseId);
            if (!$course) {
                ApiResponse::json(404, ['error' => 'curso no encontrado']);
                return;
            }
            $subject->updateCourse($course);
        }

        if (isset($body['teacher_id'])) {
            if ($body['teacher_id'] === null) {
                $subject->unassignTeacher();
            } else {
                $teacher = $this->em->find(Teacher::class, (int) $body['teacher_id']);
                if (!$teacher) {
                    ApiResponse::json(404, ['error' => 'profesor no encontrado']);
                    return;
                }
                $subject->assignTeacher($teacher);
            }
        }

        $this->repo->save($subject);
        ApiResponse::json(200, ['data' => $this->toArray($subject)]);
    }

    public function delete(int $id): void
    {
        try {
            (new DeleteSubject($this->repo))->execute($id);
            ApiResponse::noContent();
        } catch (InvalidArgumentException $e) {
            ApiResponse::json(404, ['error' => 'not found']);
        }
    }

    private function toArray(Subject $s): array
    {
        return [
            'id' => $s->getId(),
            'name' => $s->getName(),
            'course_id' => $s->getCourse()->getId(),
            'teacher_id' => $s->getTeacher()?->getId(),
        ];
    }
}
