<?php

declare(strict_types=1);

namespace School\Infrastructure\Http;

use Doctrine\ORM\EntityManagerInterface;
use School\Infrastructure\Http\Controller\StudentsApiController;
use School\Infrastructure\Http\Controller\SubjectsApiController;
use School\Infrastructure\Http\Controller\TeachersApiController;

final class ApiKernel
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function handle(ApiRequest $request): bool
    {
        $path = $request->getPath();
        $method = $request->getMethod();

        if (strpos($path, '/api') !== 0) {
            return false;
        }

        if ($method === 'GET' && $path === '/api') {
            ApiResponse::json(200, [
                'message' => 'School API running',
            ]);
            return true;
        }

        if ($method === 'GET' && $path === '/api/health') {
            ApiResponse::json(200, [
                'status' => 'ok',
            ]);
            return true;
        }

        $studentsController = new StudentsApiController($this->entityManager);
        $teachersController = new TeachersApiController($this->entityManager);
        $subjectsController = new SubjectsApiController($this->entityManager);

        if ($method === 'GET' && $path === '/api/students') {
            $studentsController->index();
            return true;
        }

        if ($method === 'POST' && $path === '/api/students') {
            $studentsController->create($request);
            return true;
        }

        if (preg_match('#^/api/students/(\d+)$#', $path, $matches) === 1) {
            $id = (int) $matches[1];

            if ($method === 'GET') {
                $studentsController->show($id);
                return true;
            }

            if ($method === 'PUT') {
                $studentsController->update($id, $request);
                return true;
            }

            if ($method === 'DELETE') {
                $studentsController->delete($id);
                return true;
            }
        }

        if ($method === 'GET' && $path === '/api/teachers') {
            $teachersController->index();
            return true;
        }

        if ($method === 'POST' && $path === '/api/teachers') {
            $teachersController->create($request);
            return true;
        }

        if (preg_match('#^/api/teachers/(\d+)$#', $path, $matches) === 1) {
            $id = (int) $matches[1];

            if ($method === 'GET') {
                $teachersController->show($id);
                return true;
            }

            if ($method === 'PUT') {
                $teachersController->update($id, $request);
                return true;
            }

            if ($method === 'DELETE') {
                $teachersController->delete($id);
                return true;
            }
        }

        if ($method === 'GET' && $path === '/api/subjects') {
            $subjectsController->index();
            return true;
        }

        if ($method === 'POST' && $path === '/api/subjects') {
            $subjectsController->create($request);
            return true;
        }

        if (preg_match('#^/api/subjects/(\d+)$#', $path, $matches) === 1) {
            $id = (int) $matches[1];

            if ($method === 'GET') {
                $subjectsController->show($id);
                return true;
            }

            if ($method === 'PUT') {
                $subjectsController->update($id, $request);
                return true;
            }

            if ($method === 'DELETE') {
                $subjectsController->delete($id);
                return true;
            }
        }

        ApiResponse::json(404, [
            'error' => 'Endpoint not found',
        ]);
        return true;
    }
}
