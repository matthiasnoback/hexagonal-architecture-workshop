<?php

declare(strict_types=1);

namespace App\Entity;

final class User
{
    private UserId $userId;

    private string $name;

    private string $emailAddress;

    private UserType $userType;

    private function __construct()
    {
    }

    public static function fromDatabaseRecord(array $record): self
    {
        $user = new self();

        $user->userId = UserId::fromString($record['userId']);
        $user->name = $record['name'];
        $user->emailAddress = $record['emailAddress'];
        $user->userType = UserType::from($record['userType']);

        return $user;
    }

    public static function create(UserId $userId, string $name, string $emailAddress, UserType $userType): self
    {
        $user = new self();

        $user->userId = $userId;
        $user->name = $name;
        $user->emailAddress = $emailAddress;
        $user->userType = $userType;

        return $user;
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

    public function userTypeIs(UserType $userType): bool
    {
        return $this->userType === $userType;
    }

    /**
     * @return array<string,string>
     */
    public function asDatabaseRecord(): array
    {
        return [
            'userId' => $this->userId->asString(),
            'name' => $this->name,
            'emailAddress' => $this->emailAddress,
            'userType' => $this->userType->name,
        ];
    }
}
