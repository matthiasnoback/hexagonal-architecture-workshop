<?php

declare(strict_types=1);

namespace App\Entity;

use Assert\Assert;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ForwardCompatibility\DriverResultStatement;

final class UserRepository
{
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
        if ($record === false) {
            throw CouldNotFindUser::withEmailAddress($emailAddress);
        }
        Assert::that($record)->isArray();

        return User::fromDatabaseRecord($record);
    }
}
