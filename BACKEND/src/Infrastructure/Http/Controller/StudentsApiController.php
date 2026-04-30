<?php

declare(strict_types=1);

namespace School\Infrastructure\Http\Controller;

use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use School\Application\UseCase\CreateStudent;
use School\Application\UseCase\DeleteStudent;
use School\Domain\Entity\Student;
use School\Infrastructure\Http\ApiRequest;
use School\Infrastructure\Http\ApiResponse;
use School\Infrastructure\Persistence\Doctrine\DoctrineStudentRepository;

final class StudentsApiController
{
    private DoctrineStudentRepository $repo;
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = new DoctrineStudentRepository($em);
    }

    public function index(): void
    {
        $students = $this->em->getRepository(Student::class)->findAll();
        ApiResponse::json(200, ['data' => array_map(fn(Student $s) => $this->toArray($s), $students)]);
    }

    public function show(int $id): void
    {
        $student = $this->repo->findById($id);
        $student ? ApiResponse::json(200, ['data' => $this->toArray($student)]) : ApiResponse::json(404, ['error' => 'Not found']);
    }

    public function create(ApiRequest $request): void
    {
        try {
            $body = $request->getBody();
            $name = trim($body['name'] ?? '');
            $email = trim($body['email'] ?? '');

            if (!$name || !$email) {
                ApiResponse::json(400, ['error' => 'name and email required']);
                return;
            }

            $student = (new CreateStudent($this->repo))->execute($name, $email);
            ApiResponse::json(201, ['data' => $this->toArray($student)]);
        } catch (\Throwable $e) {
            ApiResponse::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function update(int $id, ApiRequest $request): void
    {
        try {
            $student = $this->repo->findById($id);
            if (!$student) {
                ApiResponse::json(404, ['error' => 'Not found']);
                return;
            }

            $body = $request->getBody();
            $name = isset($body['name']) ? trim($body['name']) : null;
            $email = isset($body['email']) ? trim($body['email']) : null;

            if (($name !== null && !$name) || ($email !== null && !$email)) {
                ApiResponse::json(400, ['error' => 'Fields cannot be empty']);
                return;
            }

            if ($name !== null || $email !== null) {
                $student->updateData($name, $email);
                $this->repo->save($student);
            }

            ApiResponse::json(200, ['data' => $this->toArray($student)]);
        } catch (\Throwable $e) {
            ApiResponse::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function delete(int $id): void
    {
        try {
            (new DeleteStudent($this->repo))->execute($id);
            ApiResponse::noContent();
        } catch (InvalidArgumentException $e) {
            ApiResponse::json(404, ['error' => $e->getMessage()]);
        } catch (\Throwable $e) {
            ApiResponse::json(500, ['error' => $e->getMessage()]);
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
