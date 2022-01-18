<?php
declare(strict_types=1);

namespace Billing;

interface UsageStatistics
{
    public function numberOfMeetupsOrganized(string $organizerId, int $year, int $month): int;
}
