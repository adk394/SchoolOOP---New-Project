<?php

declare(strict_types=1);

namespace School\Infrastructure\Auth\Token;

use School\Auth\Domain\User;

final class JwtTokenGenerator
{
    public function __construct(private readonly string $secret, private readonly int $ttl = 3600)
    {
        if ($this->secret === '') {
            throw new \InvalidArgumentException('JWT secret cannot be empty');
        }
    }

    public function generate(User $user): string
    {
        $header = $this->encode([ 'alg' => 'HS256', 'typ' => 'JWT' ]);
        $payload = $this->encode([
            'sub' => $user->getId()->value(),
            'email' => $user->getEmail(),
            'name' => $user->getName(),
            'iat' => time(),
            'exp' => time() + $this->ttl,
        ]);

        $signature = $this->sign("{$header}.{$payload}");

        return "{$header}.{$payload}.{$signature}";
    }

    private function encode(array $data): string
    {
        return rtrim(strtr(base64_encode(json_encode($data, JSON_THROW_ON_ERROR)), '+/', '-_'), '=');
    }

    private function sign(string $input): string
    {
        return rtrim(strtr(base64_encode(hash_hmac('sha256', $input, $this->secret, true)), '+/', '-_'), '=');
    }
}
