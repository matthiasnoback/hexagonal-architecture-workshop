<?php

declare(strict_types=1);

namespace App\Entity;

use Assert\Assert;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ForwardCompatibility\DriverResultStatement;

final class UserRepository
{
    final public const ORGANIZER_ID = 1;

    final public const REGULAR_USER_ID = 2;

    /**
     * @var array<int,array{userId:int,name:string}>
     */
    private array $records = [
        self::ORGANIZER_ID => [
            'userId' => self::ORGANIZER_ID,
            'name' => 'Organizer',
            'emailAddress' => 'organizer@example.com',
        ],
        self::REGULAR_USER_ID => [
            'userId' => self::REGULAR_USER_ID,
            'name' => 'Regular user',
            'emailAddress' => 'user@example.com',
        ],
    ];

    public function __construct(
        private readonly Connection $connection
    ) {
    }

    public function getById(UserId $id): User
    {
        $result = $this->connection->executeQuery('SELECT * FROM users WHERE userId = ?', [$id->asInt()]);
        Assert::that($result)->isInstanceOf(DriverResultStatement::class, 'User not found');

        $record = $result->fetchAssociative();
        Assert::that($record)->isArray();

        return User::fromDatabaseRecord($record);
    }

    public function getByEmailAddress(string $emailAddress): User
    {
        $result = $this->connection->executeQuery('SELECT * FROM users WHERE emailAddress = ?', [$emailAddress]);
        Assert::that($result)->isInstanceOf(DriverResultStatement::class, 'User not found');

        $record = $result->fetchAssociative();
        Assert::that($record)->isArray();

        return User::fromDatabaseRecord($record);
    }

    /**
     * @return array<User>
     */
    public function findAll(): array
    {
        return array_map(fn (array $record) => User::fromDatabaseRecord($record), $this->records);
    }

    public function getOrganizerId(): UserId
    {
        return UserId::fromInt(self::ORGANIZER_ID);
    }
}
