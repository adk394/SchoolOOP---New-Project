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
    private DoctrineStudentRepository $studentRepository;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        $this->studentRepository = new DoctrineStudentRepository($entityManager);
    }

    public function index(): void
    {
        $items = $this->entityManager->getRepository(Student::class)->findAll();
        $result = array_map(fn (Student $student): array => $this->toArray($student), $items);

        ApiResponse::json(200, ['data' => $result]);
    }

    public function show(int $id): void
    {
        $student = $this->studentRepository->findById($id);
        if ($student === null) {
            ApiResponse::json(404, ['error' => 'Student not found']);
            return;
        }

        ApiResponse::json(200, ['data' => $this->toArray($student)]);
    }

    public function create(ApiRequest $request): void
    {
        $body = $request->getBody();
        $name = trim((string) ($body['name'] ?? ''));
        $email = trim((string) ($body['email'] ?? ''));

        if ($name === '' || $email === '') {
            ApiResponse::json(400, ['error' => 'name and email are required']);
            return;
        }

        try {
            $student = (new CreateStudent($this->studentRepository))->execute($name, $email);
            ApiResponse::json(201, ['data' => $this->toArray($student)]);
        } catch (\Throwable $e) {
            ApiResponse::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function update(int $id, ApiRequest $request): void
    {
        $student = $this->studentRepository->findById($id);
        if ($student === null) {
            ApiResponse::json(404, ['error' => 'Student not found']);
            return;
        }

        $body = $request->getBody();
        $name = array_key_exists('name', $body) ? trim((string) $body['name']) : null;
        $email = array_key_exists('email', $body) ? trim((string) $body['email']) : null;

        if ($name === '' || $email === '') {
            ApiResponse::json(400, ['error' => 'name/email cannot be empty']);
            return;
        }

        try {
            $student->updateData($name, $email);
            $this->studentRepository->save($student);
            ApiResponse::json(200, ['data' => $this->toArray($student)]);
        } catch (InvalidArgumentException $e) {
            ApiResponse::json(400, ['error' => $e->getMessage()]);
        } catch (\Throwable $e) {
            ApiResponse::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function delete(int $id): void
    {
        try {
            (new DeleteStudent($this->studentRepository))->execute($id);
            ApiResponse::noContent();
        } catch (InvalidArgumentException $e) {
            ApiResponse::json(404, ['error' => $e->getMessage()]);
        } catch (\Throwable $e) {
            ApiResponse::json(500, ['error' => $e->getMessage()]);
        }
    }

    private function toArray(Student $student): array
    {
        return [
            'id' => $student->getId(),
            'name' => $student->getName(),
            'email' => $student->getEmail(),
            'course_id' => $student->getCourse()?->getId(),
        ];
    }
}
