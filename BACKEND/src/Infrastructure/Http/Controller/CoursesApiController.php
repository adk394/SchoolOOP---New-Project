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
    private DoctrineCourseRepository $repo;
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = new DoctrineCourseRepository($em);
    }

    public function index(): void
    {
        $courses = $this->em->getRepository(Course::class)->findAll();
        ApiResponse::json(200, ['data' => array_map(fn(Course $c) => $this->toArray($c), $courses)]);
    }

    public function show(int $id): void
    {
        $course = $this->repo->findById($id);
        $course ? ApiResponse::json(200, ['data' => $this->toArray($course)]) : ApiResponse::json(404, ['error' => 'Not found']);
    }

    public function create(ApiRequest $request): void
    {
        try {
            $body = $request->getBody();
            $name = trim($body['name'] ?? '');

            if (!$name) {
                ApiResponse::json(400, ['error' => 'name required']);
                return;
            }

            $course = (new CreateCourse($this->repo))->execute($name);
            ApiResponse::json(201, ['data' => $this->toArray($course)]);
        } catch (\Throwable $e) {
            ApiResponse::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function update(int $id, ApiRequest $request): void
    {
        try {
            $course = $this->repo->findById($id);
            if (!$course) {
                ApiResponse::json(404, ['error' => 'Not found']);
                return;
            }

            $body = $request->getBody();
            if (isset($body['name'])) {
                $name = trim($body['name']);
                if (!$name) {
                    ApiResponse::json(400, ['error' => 'name cannot be empty']);
                    return;
                }
                $course->updateName($name);
                $this->repo->save($course);
            }

            ApiResponse::json(200, ['data' => $this->toArray($course)]);
        } catch (\Throwable $e) {
            ApiResponse::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function delete(int $id): void
    {
        try {
            (new DeleteCourse($this->repo))->execute($id);
            ApiResponse::noContent();
        } catch (InvalidArgumentException $e) {
            ApiResponse::json(404, ['error' => $e->getMessage()]);
        } catch (\Throwable $e) {
            ApiResponse::json(500, ['error' => $e->getMessage()]);
        }
    }

    private function toArray(Course $c): array
    {
        return ['id' => $c->getId(), 'name' => $c->getName()];
    }
}
