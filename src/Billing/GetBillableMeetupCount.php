<?php

declare(strict_types=1);

namespace Billing;

interface GetBillableMeetupCount
{
    public function get(int $year, int $month, string $organizerId): int;
}
