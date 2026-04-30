<?php

declare(strict_types=1);

namespace School\Auth\Infrastructure\Auth\Token;

final class TokenValidator
{
    public function __construct(private readonly string $secret)
    {
        if ($this->secret === '') {
            throw new \InvalidArgumentException('JWT secret cannot be empty');
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function validate(string $token): array
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            throw new \RuntimeException('Invalid JWT format');
        }

        [$header, $payload, $signature] = $parts;
        $expected = $this->sign("{$header}.{$payload}");

        if (!hash_equals($expected, $signature)) {
            throw new \RuntimeException('Invalid JWT signature');
        }

        $payloadData = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);

        if (!is_array($payloadData)) {
            throw new \RuntimeException('Invalid JWT payload');
        }

        if (isset($payloadData['exp']) && time() >= (int) $payloadData['exp']) {
            throw new \RuntimeException('JWT token expired');
        }

        return $payloadData;
    }

    private function sign(string $input): string
    {
        return rtrim(strtr(base64_encode(hash_hmac('sha256', $input, $this->secret, true)), '+/', '-_'), '=');
    }
}
