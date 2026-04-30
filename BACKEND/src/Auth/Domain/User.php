<?php

declare(strict_types=1);

namespace School\Auth\Domain;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
final class User
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string', unique: true, nullable: true)]
    private ?string $googleId = null;

    #[ORM\Column(type: 'string', unique: true)]
    private string $email;

    #[ORM\Column(type: 'string')]
    private string $name;

    public function __construct(UserId $id, ?string $googleId, string $email, string $name)
    {
        $this->id = $id->value();
        $this->googleId = $googleId;
        $this->email = $email;
        $this->name = $name;
    }

    public function getId(): UserId
    {
        return new UserId($this->id);
    }

    public function getGoogleId(): ?string
    {
        return $this->googleId;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function updateData(?string $name, ?string $email, ?string $googleId): void
    {
        if ($name !== null) {
            $this->name = $name;
        }

        if ($email !== null) {
            $this->email = $email;
        }

        if ($googleId !== null) {
            $this->googleId = $googleId;
        }
    }

    public function getName(): string
    {
        return $this->name;
    }
}
