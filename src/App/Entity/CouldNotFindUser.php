<?php

declare(strict_types=1);

namespace App\Entity;

use RuntimeException;

final class CouldNotFindUser extends RuntimeException
{
    public static function withEmailAddress(string $emailAddress): self
    {
        return new self(sprintf('Could not find user with email address "%s"', $emailAddress));
    }

    public static function withId(UserId $id): self
    {
        return new self(sprintf('Could not find user with ID "%s"', $id->asString()));
    }
}
