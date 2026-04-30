<?php

declare(strict_types=1);

namespace School\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use School\Auth\Domain\AuthRepository;
use School\Auth\Domain\User;

final class DoctrineUserRepository implements AuthRepository
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function save(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function findByGoogleId(string $googleId): ?User
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(['googleId' => $googleId]);
    }

    public function findById(string $id): ?User
    {
        return $this->entityManager->find(User::class, $id);
    }

    /** @return User[] */
    public function findAll(): array
    {
        return $this->entityManager->getRepository(User::class)->findAll();
    }

    public function delete(User $user): void
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }
}
