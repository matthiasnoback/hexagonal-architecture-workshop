<?php
declare(strict_types=1);

namespace Billing;

interface MeetupRepositoryInterface
{
    public function countMeetupsPerMonth(
        InvoicePeriod $invoicePeriod,
        string $organizerId
    ): int;
}
