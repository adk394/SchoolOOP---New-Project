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
    private DoctrineTeacherRepository $repo;
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = new DoctrineTeacherRepository($em);
    }

    public function index(): void
    {
        $teachers = $this->em->getRepository(Teacher::class)->findAll();
        ApiResponse::json(200, ['data' => array_map(fn(Teacher $t) => $this->toArray($t), $teachers)]);
    }

    public function show(int $id): void
    {
        $teacher = $this->repo->findById($id);
        $teacher ? ApiResponse::json(200, ['data' => $this->toArray($teacher)]) : ApiResponse::json(404, ['error' => 'Not found']);
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

            $teacher = (new CreateTeacher($this->repo))->execute($name, $email);
            ApiResponse::json(201, ['data' => $this->toArray($teacher)]);
        } catch (\Throwable $e) {
            ApiResponse::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function update(int $id, ApiRequest $request): void
    {
        try {
            $teacher = $this->repo->findById($id);
            if (!$teacher) {
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
                $teacher->updateData($name, $email);
                $this->repo->save($teacher);
            }

            ApiResponse::json(200, ['data' => $this->toArray($teacher)]);
        } catch (\Throwable $e) {
            ApiResponse::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function delete(int $id): void
    {
        try {
            (new DeleteTeacher($this->repo))->execute($id);
            ApiResponse::noContent();
        } catch (InvalidArgumentException $e) {
            ApiResponse::json(404, ['error' => $e->getMessage()]);
        } catch (\Throwable $e) {
            ApiResponse::json(500, ['error' => $e->getMessage()]);
        }
    }

    private function toArray(Teacher $t): array
    {
        return [
            'id' => $t->getId(),
            'name' => $t->getName(),
            'email' => $t->getEmail(),
        ];
    }
}
