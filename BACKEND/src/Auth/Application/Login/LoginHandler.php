<?php

declare(strict_types=1);

namespace School\Auth\Application\Login;

use School\Auth\Domain\AuthRepository;
use School\Auth\Domain\User;
use School\Auth\Domain\UserId;
use School\Auth\Infrastructure\Auth\Token\JwtTokenGenerator;

final class LoginHandler
{
    public function __construct(
        private readonly AuthRepository $authRepository,
        private readonly JwtTokenGenerator $tokenGenerator
    ) {
    }

    public function handle(LoginCommand $command): string
    {
        $user = $this->authRepository->findByGoogleId($command->googleId);

        if ($user === null) {
            $user = new User(
                UserId::generate(),
                $command->googleId,
                $command->email,
                $command->name
            );
            $this->authRepository->save($user);
        }

        return $this->tokenGenerator->generate($user);
    }
}
