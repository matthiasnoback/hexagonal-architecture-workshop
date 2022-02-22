<?php
declare(strict_types=1);

namespace App;

final class CreateInvoice
{
    public function __construct(
        private string $organizerId,
        private int $year,
        private int $month,
    )
    {
    }

    public function organizerId(): string
    {
        return $this->organizerId;
    }

    public function year(): int
    {
        return $this->year;
    }

    public function month(): int
    {
        return $this->month;
    }
}
