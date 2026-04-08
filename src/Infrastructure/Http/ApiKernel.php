<?php

declare(strict_types=1);

namespace School\Infrastructure\Http;

final class ApiKernel
{
    public function handle(ApiRequest $request): bool
    {
        $path = $request->getPath();

        if (strpos($path, '/api') !== 0) {
            return false;
        }

        if ($request->getMethod() === 'GET' && $path === '/api') {
            ApiResponse::json(200, [
                'message' => 'School API running',
            ]);
            return true;
        }

        if ($request->getMethod() === 'GET' && $path === '/api/health') {
            ApiResponse::json(200, [
                'status' => 'ok',
            ]);
            return true;
        }

        ApiResponse::json(404, [
            'error' => 'Endpoint not found',
        ]);
        return true;
    }
}
