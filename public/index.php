<?php

declare(strict_types=1);

use School\Infrastructure\AppFactory;
use School\Infrastructure\Controller\SchoolController;
use School\Infrastructure\Http\ApiKernel;
use School\Infrastructure\Http\ApiRequest;

require_once __DIR__ . '/../vendor/autoload.php';

$entityManager = require __DIR__ . '/../config/doctrine.php';

$apiKernel = new ApiKernel($entityManager);
if ($apiKernel->handle(new ApiRequest())) {
    exit;
}

$factory = new AppFactory($entityManager);
$controller = new SchoolController($factory);

$success = null;
$error = null;
$route = (string) ($_GET['route'] ?? 'home');

$validRoutes = [
    'home',
    'create-student',
    'create-course',
    'create-subject',
    'create-teacher',
    'enroll-student',
    'assign-teacher-to-subject',
    'delete-student',
    'delete-course',
    'delete-subject',
    'delete-teacher',
];

if (!in_array($route, $validRoutes, true)) {
    $route = 'home';
    $error = 'Ruta no valida';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $controller->handle($_POST);
    $success = $result['success'] ?? null;
    $error = $result['error'] ?? null;
}

require __DIR__ . '/../src/Infrastructure/View/home.php';
