<?php

declare(strict_types=1);

namespace School\Auth\Domain;

final class HashedPassword
{
    private function __construct(private readonly string $hash)
    {
    }

    public static function fromPlainText(string $plainText): self
    {
        return new self(password_hash($plainText, PASSWORD_DEFAULT));
    }

    public static function fromHash(string $hash): self
    {
        return new self($hash);
    }

    public function verify(string $plainText): bool
    {
        return password_verify($plainText, $this->hash);
    }

    public function value(): string
    {
        return $this->hash;
    }
}
