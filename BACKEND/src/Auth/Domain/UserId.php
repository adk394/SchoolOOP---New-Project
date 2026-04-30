<?php

declare(strict_types=1);

namespace School\Auth\Domain;

final class UserId
{
    public function __construct(private readonly string $value)
    {
        if ($this->value === '') {
            throw new \InvalidArgumentException('UserId cannot be empty');
        }
    }

    public static function generate(): self
    {
        return new self(bin2hex(random_bytes(16)));
    }

    public function value(): string
    {
        return $this->value;
    }
}
