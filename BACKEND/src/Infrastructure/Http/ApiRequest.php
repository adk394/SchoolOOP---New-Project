<?php

declare(strict_types=1);

namespace School\Infrastructure\Http;

final class ApiRequest
{
    private string $method;
    private string $path;
    private array $body;

    public function __construct()
    {
        $this->method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        $requestUri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
        $this->path = (string) (parse_url($requestUri, PHP_URL_PATH) ?? '/');

        $rawBody = file_get_contents('php://input');
        $decoded = json_decode($rawBody !== false ? $rawBody : '', true);
        $this->body = is_array($decoded) ? $decoded : [];
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getBody(): array
    {
        return $this->body;
    }
}
