<?php

declare(strict_types=1);

namespace School\Infrastructure\Http\Controller;

use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use School\Auth\Domain\User;
use School\Auth\Domain\UserId;
use School\Infrastructure\Http\ApiRequest;
use School\Infrastructure\Http\ApiResponse;
use School\Infrastructure\Persistence\Doctrine\DoctrineUserRepository;

final class UsersApiController
{
    private DoctrineUserRepository $userRepository;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        $this->userRepository = new DoctrineUserRepository($this->entityManager);
    }

    public function index(): void
    {
        $users = $this->userRepository->findAll();
        $payload = array_map(fn (User $user): array => $this->toArray($user), $users);
        ApiResponse::json(200, ['data' => $payload]);
    }

    public function show(string $id): void
    {
        $user = $this->userRepository->findById($id);
        if (!$user) {
            ApiResponse::json(404, ['error' => 'not found']);
            return;
        }
        ApiResponse::json(200, ['data' => $this->toArray($user)]);
    }

    public function create(ApiRequest $request): void
    {
        $body = $request->getBody();
        $name = trim((string) ($body['name'] ?? ''));
        $email = trim((string) ($body['email'] ?? ''));
        $googleId = isset($body['google_id']) ? trim((string) $body['google_id']) : null;

        if (!$name || !$email) {
            ApiResponse::json(400, ['error' => 'faltan datos']);
            return;
        }

        $user = new User(UserId::generate(), $googleId === '' ? null : $googleId, $email, $name);
        $this->userRepository->save($user);
        ApiResponse::json(201, ['data' => $this->toArray($user)]);
    }

    public function update(string $id, ApiRequest $request): void
    {
        $user = $this->userRepository->findById($id);
        if (!$user) {
            ApiResponse::json(404, ['error' => 'not found']);
            return;
        }

        $body = $request->getBody();
        $name = array_key_exists('name', $body) ? trim((string) $body['name']) : null;
        $email = array_key_exists('email', $body) ? trim((string) $body['email']) : null;
        $googleId = array_key_exists('google_id', $body) ? trim((string) $body['google_id']) : null;

        if ($name === '' || $email === '') {
            ApiResponse::json(400, ['error' => 'datos invalidos']);
            return;
        }

        $user->updateData($name, $email, $googleId);
        $this->userRepository->save($user);
        ApiResponse::json(200, ['data' => $this->toArray($user)]);
    }

    public function delete(string $id): void
    {
        $user = $this->userRepository->findById($id);
        if (!$user) {
            ApiResponse::json(404, ['error' => 'not found']);
            return;
        }
        $this->userRepository->delete($user);
        ApiResponse::noContent();
    }

    private function toArray(User $user): array
    {
        return [
            'id' => $user->getId()->value(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'google_id' => $user->getGoogleId(),
        ];
    }
}
