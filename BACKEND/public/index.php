<?php

declare(strict_types=1);

use School\Infrastructure\Http\ApiKernel;
use School\Infrastructure\Http\ApiRequest;

require_once __DIR__ . '/../vendor/autoload.php';

$allowedOrigin = getenv('SCHOOL_FRONTEND_ORIGIN') ?: 'http://localhost:8001';
header('Access-Control-Allow-Origin: ' . $allowedOrigin);
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$entityManager = require __DIR__ . '/../config/doctrine.php';

$apiKernel = new ApiKernel($entityManager);
if ($apiKernel->handle(new ApiRequest())) {
    exit;
}

http_response_code(404);
echo 'abrir vista desde el frontend!!!';
