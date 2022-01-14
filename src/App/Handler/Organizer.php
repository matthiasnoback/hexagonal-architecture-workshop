<?php

declare(strict_types=1);

namespace App\Handler;

final class Organizer
{
    public function __construct(
        private readonly string $id,
        private readonly string $name
    ) {
    }

    public function organizerId(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }
}
