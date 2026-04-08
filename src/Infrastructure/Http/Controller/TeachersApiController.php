<?php

declare(strict_types=1);

namespace School\Infrastructure\Http\Controller;

use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use School\Application\UseCase\CreateTeacher;
use School\Application\UseCase\DeleteTeacher;
use School\Domain\Entity\Teacher;
use School\Infrastructure\Http\ApiRequest;
use School\Infrastructure\Http\ApiResponse;
use School\Infrastructure\Persistence\Doctrine\DoctrineTeacherRepository;

final class TeachersApiController
{
    private DoctrineTeacherRepository $teacherRepository;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        $this->teacherRepository = new DoctrineTeacherRepository($entityManager);
    }

    public function index(): void
    {
        $items = $this->entityManager->getRepository(Teacher::class)->findAll();
        $result = array_map(fn (Teacher $teacher): array => $this->toArray($teacher), $items);

        ApiResponse::json(200, ['data' => $result]);
    }

    public function show(int $id): void
    {
        $teacher = $this->teacherRepository->findById($id);
        if ($teacher === null) {
            ApiResponse::json(404, ['error' => 'Teacher not found']);
            return;
        }

        ApiResponse::json(200, ['data' => $this->toArray($teacher)]);
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
            $teacher = (new CreateTeacher($this->teacherRepository))->execute($name, $email);
            ApiResponse::json(201, ['data' => $this->toArray($teacher)]);
        } catch (\Throwable $e) {
            ApiResponse::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function update(int $id, ApiRequest $request): void
    {
        $teacher = $this->teacherRepository->findById($id);
        if ($teacher === null) {
            ApiResponse::json(404, ['error' => 'Teacher not found']);
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
            $teacher->updateData($name, $email);
            $this->teacherRepository->save($teacher);
            ApiResponse::json(200, ['data' => $this->toArray($teacher)]);
        } catch (InvalidArgumentException $e) {
            ApiResponse::json(400, ['error' => $e->getMessage()]);
        } catch (\Throwable $e) {
            ApiResponse::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function delete(int $id): void
    {
        try {
            (new DeleteTeacher($this->teacherRepository))->execute($id);
            ApiResponse::noContent();
        } catch (InvalidArgumentException $e) {
            ApiResponse::json(404, ['error' => $e->getMessage()]);
        } catch (\Throwable $e) {
            ApiResponse::json(500, ['error' => $e->getMessage()]);
        }
    }

    private function toArray(Teacher $teacher): array
    {
        return [
            'id' => $teacher->getId(),
            'name' => $teacher->getName(),
            'email' => $teacher->getEmail(),
        ];
    }
}
