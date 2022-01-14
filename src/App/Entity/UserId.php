<?php

declare(strict_types=1);

namespace App\Entity;

use Assert\Assert;

final class UserId
{
    private function __construct(
        private readonly string $id
    ) {
        Assert::that($id)->uuid();
    }

    public static function fromString(string $id): self
    {
        return new self($id);
    }

    public function asString(): string
    {
        return $this->id;
    }
}
