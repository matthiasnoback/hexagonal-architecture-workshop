<?php
declare(strict_types=1);

namespace Billing\ViewModel;

final class Organizer
{
    public function __construct(
        private string $id,
        private string $name
    ) {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }
}
