<?php

declare(strict_types=1);

namespace App\Entity;

final class UserHasSignedUp
{
    public function __construct(
        private readonly UserId $userId,
        private readonly string $name,
        private readonly string $emailAddress,
        private readonly UserType $userType
    ) {
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function emailAddress(): string
    {
        return $this->emailAddress;
    }

    public function userType(): UserType
    {
        return $this->userType;
    }
}
