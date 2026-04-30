<?php

declare(strict_types=1);

namespace School\Auth\Domain;

interface AuthRepository
{
    public function save(User $user): void;

    public function findByGoogleId(string $googleId): ?User;

    public function findById(string $id): ?User;

    /** @return User[] */
    public function findAll(): array;

    public function delete(User $user): void;
}
