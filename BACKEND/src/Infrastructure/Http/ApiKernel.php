<?php

declare(strict_types=1);

namespace School\Infrastructure\Http;

use Doctrine\ORM\EntityManagerInterface;
use School\Infrastructure\Http\Controller\AuthController;
use School\Infrastructure\Http\Controller\StudentsApiController;
use School\Infrastructure\Http\Controller\SubjectsApiController;
use School\Infrastructure\Http\Controller\TeachersApiController;
use School\Infrastructure\Http\Controller\CoursesApiController;
use School\Infrastructure\AppFactory;

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
            ApiResponse::json(200, ['message' => 'School API running']);
            return true;
        }

        if ($method === 'GET' && $path === '/api/health') {
            ApiResponse::json(200, ['status' => 'ok']);
            return true;
        }

        $authHandled = $this->routeAuth($path, $method, $request);
        if ($authHandled) {
            return true;
        }

        $handled = $this->routeResource('students', $path, $method, $request, new StudentsApiController($this->entityManager))
            || $this->routeResource('teachers', $path, $method, $request, new TeachersApiController($this->entityManager))
            || $this->routeResource('subjects', $path, $method, $request, new SubjectsApiController($this->entityManager))
            || $this->routeResource('courses', $path, $method, $request, new CoursesApiController($this->entityManager));

        if (!$handled) {
            ApiResponse::json(404, ['error' => 'Endpoint not found']);
        }

        return true;
    }

    private function routeAuth(string $path, string $method, ApiRequest $request): bool
    {
        if (strpos($path, '/api/auth/') !== 0) {
            return false;
        }

        try {
            $appFactory = new AppFactory($this->entityManager);
            $jwtSecret = getenv('JWT_SECRET') ?: 'cambia-esto-en-produccion';
            $oauthConfig = require __DIR__ . '/../../../config/oauth.php';
            $googleConfig = $oauthConfig['google'];

            $authController = new AuthController(
                $appFactory->createLoginHandler($jwtSecret),
                $googleConfig['client_id'],
                $googleConfig['client_secret'],
                $googleConfig['redirect_uri']
            );

            $action = substr($path, strlen('/api/auth/'));

            if ($action === 'login' && $method === 'GET') {
                $authController->login();
                return true;
            }

            if ($action === 'callback' && $method === 'GET') {
                $authController->callback($request);
                return true;
            }

            ApiResponse::json(404, ['error' => 'Auth endpoint not found: ' . $action]);
            return true;
        } catch (\Throwable $e) {
            ApiResponse::json(500, ['error' => 'Auth error: ' . $e->getMessage()]);
            return true;
        }
    }

    private function routeResource(string $resource, string $path, string $method, ApiRequest $request, object $controller): bool
    {
        if (!preg_match("#^/api/{$resource}(?:/(\d+))?$#", $path, $matches)) {
            return false;
        }

        $id = isset($matches[1]) ? (int) $matches[1] : null;

        if ($id === null) {
            if ($method === 'GET') {
                $controller->index();
                return true;
            }
            if ($method === 'POST') {
                $controller->create($request);
                return true;
            }
        } else {
            if ($method === 'GET') {
                $controller->show($id);
                return true;
            }
            if ($method === 'PUT') {
                $controller->update($id, $request);
                return true;
            }
            if ($method === 'DELETE') {
                $controller->delete($id);
                return true;
            }
        }

        return false;
    }
}
