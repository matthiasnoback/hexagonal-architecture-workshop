<?php
declare(strict_types=1);

namespace AppTest\Billing;

use Billing\InvoicePeriod;
use Billing\MeetupRepositoryInterface;

final class MeetupRepositoryStub implements MeetupRepositoryInterface
{
    private int $count;

    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    public function countMeetupsPerMonth(InvoicePeriod $invoicePeriod, string $organizerId): int
    {
        return $this->count;
    }
}
