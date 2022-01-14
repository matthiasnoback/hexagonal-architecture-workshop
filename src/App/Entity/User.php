<?php

declare(strict_types=1);

namespace App\Entity;

final class User
{
    private int $userId;

    private string $name;

    private string $emailAddress;

    private UserType $userType;

    private function __construct()
    {
    }

    public static function fromDatabaseRecord(array $record): self
    {
        $user = new self();

        $user->userId = (int) $record['userId'];
        $user->name = $record['name'];
        $user->emailAddress = $record['emailAddress'];
        $user->userType = UserType::from($record['userType']);

        return $user;
    }

    public function userId(): UserId
    {
        return UserId::fromInt($this->userId);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function emailAddress(): string
    {
        return $this->emailAddress;
    }

    public function userTypeIs(UserType $userType): bool
    {
        return $this->userType === $userType;
    }
}
