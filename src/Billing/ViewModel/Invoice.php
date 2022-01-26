<?php
declare(strict_types=1);

namespace Billing\ViewModel;

final class Invoice
{
    public function __construct(
        private readonly int $invoiceId,
        private readonly string $organizerId,
        private readonly string $period,
        private readonly string $amount,
    ) {
    }

    public function invoiceId(): int
    {
        return $this->invoiceId;
    }

    public function organizerId(): string
    {
        return $this->organizerId;
    }

    public function period(): string
    {
        return $this->period;
    }

    public function amount(): string
    {
        return $this->amount;
    }
}
