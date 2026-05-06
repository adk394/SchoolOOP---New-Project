<?php

declare(strict_types=1);

namespace School\Infrastructure\Http\Controller;

use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use School\Application\UseCase\CreateStudent;
use School\Application\UseCase\DeleteStudent;
use School\Application\UseCase\EnrollStudent;
use School\Domain\Entity\Student;
use School\Infrastructure\Http\ApiRequest;
use School\Infrastructure\Http\ApiResponse;
use School\Infrastructure\Persistence\Doctrine\DoctrineCourseRepository;
use School\Infrastructure\Persistence\Doctrine\DoctrineStudentRepository;

final class StudentsApiController
{
    private DoctrineStudentRepository $repo;
    private DoctrineCourseRepository $courseRepo;
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = new DoctrineStudentRepository($em);
        $this->courseRepo = new DoctrineCourseRepository($em);
    }

    public function index(): void
    {
        $students = $this->em->getRepository(Student::class)->findAll();
        ApiResponse::json(200, ['data' => array_map(fn(Student $s) => $this->toArray($s), $students)]);
    }

    public function show(int $id): void
    {
        $student = $this->repo->findById($id);
        $student 
            ? ApiResponse::json(200, ['data' => $this->toArray($student)]) 
            : ApiResponse::json(404, ['error' => 'not found']);
    }

    public function create(ApiRequest $request): void
    {
        $body = $request->getBody();
        $name = trim($body['name'] ?? '');
        $email = trim($body['email'] ?? '');

        if (!$name || !$email) {
            ApiResponse::json(400, ['error' => 'faltan datos']);
            return;
        }

        $student = (new CreateStudent($this->repo))->execute($name, $email);
        ApiResponse::json(201, ['data' => $this->toArray($student)]);
    }

    public function update(int $id, ApiRequest $request): void
    {
        $student = $this->repo->findById($id);
        if (!$student) {
            ApiResponse::json(404, ['error' => 'not found']);
            return;
        }

        $body = $request->getBody();
        $name = isset($body['name']) ? trim($body['name']) : null;
        $email = isset($body['email']) ? trim($body['email']) : null;

        if (($name !== null && !$name) || ($email !== null && !$email)) {
            ApiResponse::json(400, ['error' => 'datos invalidos']);
            return;
        }

        if ($name !== null || $email !== null) {
            $student->updateData($name, $email);
            $this->repo->save($student);
        }

        if (isset($body['course_id'])) {
            $courseId = (int) $body['course_id'];
            if ($courseId > 0) {
                (new EnrollStudent($this->repo, $this->courseRepo))->execute($id, $courseId);
            }
        }

        $this->em->refresh($student);
        ApiResponse::json(200, ['data' => $this->toArray($student)]);
    }

    public function enroll(int $id, ApiRequest $request): void
    {
        $student = $this->repo->findById($id);
        if (!$student) {
            ApiResponse::json(404, ['error' => 'not found']);
            return;
        }

        $body = $request->getBody();
        $courseId = isset($body['course_id']) ? (int) $body['course_id'] : 0;

        if ($courseId <= 0) {
            ApiResponse::json(400, ['error' => 'course_id invalido']);
            return;
        }

        $course = $this->courseRepo->findById($courseId);
        if (!$course) {
            ApiResponse::json(404, ['error' => 'curso no encontrado']);
            return;
        }

        (new EnrollStudent($this->repo, $this->courseRepo))->execute($id, $courseId);
        ApiResponse::json(200, ['success' => 'estudiante matriculado']);
    }

    public function delete(int $id): void
    {
        try {
            (new DeleteStudent($this->repo))->execute($id);
            ApiResponse::noContent();
        } catch (InvalidArgumentException $e) {
            ApiResponse::json(404, ['error' => 'not found']);
        }
    }

    private function toArray(Student $s): array
    {
        return [
            'id' => $s->getId(),
            'name' => $s->getName(),
            'email' => $s->getEmail(),
            'course_id' => $s->getCourse()?->getId(),
        ];
    }
}
