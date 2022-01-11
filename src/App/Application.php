<?php

declare(strict_types=1);

namespace App;

use Doctrine\DBAL\Connection;

final class Application implements ApplicationInterface
{
    public function __construct(
        private readonly Connection $connection
    ) {
    }

    public function signUp(SignUp $command): void
    {
        $this->connection->insert(
            'users',
            [
                'name' => $command->name(),
                'emailAddress' => $command->emailAddress(),
                'userType' => $command->userType()
                    ->name,
            ]
        );
    }
}
