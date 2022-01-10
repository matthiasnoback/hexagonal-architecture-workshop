<?php

declare(strict_types=1);

namespace App\Entity;

final class UserId
{
    private function __construct(
        private readonly int $id
    ) {
    }

    public static function fromInt(int $id): self
    {
        return new self($id);
    }

    public function asInt(): int
    {
        return $this->id;
    }
}
