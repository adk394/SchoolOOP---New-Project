<?php

declare(strict_types=1);

namespace School\Infrastructure\Http;

final class ApiResponse
{
    public static function json(int $statusCode, array $data): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public static function noContent(): void
    {
        http_response_code(204);
    }
}
