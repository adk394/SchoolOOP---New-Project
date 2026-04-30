<?php

declare(strict_types=1);

namespace School\Auth\Application\Login;

final class LoginCommand
{
    public function __construct(
        public readonly string $googleId,
        public readonly string $email,
        public readonly string $name
    ) {
    }
}
