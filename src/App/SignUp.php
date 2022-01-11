<?php

declare(strict_types=1);

namespace App;

use App\Entity\UserType;

final class SignUp
{
    public function __construct(
        private readonly string $name,
        private readonly string $emailAddress,
        private readonly string $userType,
    ) {
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
        return UserType::from($this->userType);
    }
}
