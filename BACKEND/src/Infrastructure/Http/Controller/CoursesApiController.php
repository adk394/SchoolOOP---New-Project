<?php

declare(strict_types=1);

namespace School\Infrastructure\Http\Controller;

use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use School\Application\UseCase\CreateCourse;
use School\Application\UseCase\DeleteCourse;
use School\Domain\Entity\Course;
use School\Infrastructure\Http\ApiRequest;
use School\Infrastructure\Http\ApiResponse;
use School\Infrastructure\Persistence\Doctrine\DoctrineCourseRepository;

final class CoursesApiController
{
    private DoctrineCourseRepository $courseRepository;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        $this->courseRepository = new DoctrineCourseRepository($entityManager);
    }

    public function index(): void
    {
        $items = $this->entityManager->getRepository(Course::class)->findAll();
        $result = array_map(fn (Course $c): array => $this->toArray($c), $items);

        ApiResponse::json(200, ['data' => $result]);
    }

    public function show(int $id): void
    {
        $course = $this->courseRepository->findById($id);
        if ($course === null) {
            ApiResponse::json(404, ['error' => 'Course not found']);
            return;
        }

        ApiResponse::json(200, ['data' => $this->toArray($course)]);
    }

    public function create(ApiRequest $request): void
    {
        $body = $request->getBody();
        $name = trim((string) ($body['name'] ?? ''));

        if ($name === '') {
            ApiResponse::json(400, ['error' => 'name is required']);
            return;
        }

        try {
            $course = (new CreateCourse($this->courseRepository))->execute($name);
            ApiResponse::json(201, ['data' => $this->toArray($course)]);
        } catch (\Throwable $e) {
            ApiResponse::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function delete(int $id): void
    {
        try {
            (new DeleteCourse($this->courseRepository))->execute($id);
            ApiResponse::noContent();
        } catch (InvalidArgumentException $e) {
            ApiResponse::json(404, ['error' => $e->getMessage()]);
        } catch (\Throwable $e) {
            ApiResponse::json(500, ['error' => $e->getMessage()]);
        }
    }

    private function toArray(Course $course): array
    {
        return [
            'id' => $course->getId(),
            'name' => $course->getName(),
        ];
    }
}
