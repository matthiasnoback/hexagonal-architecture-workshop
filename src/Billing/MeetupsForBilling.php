<?php
declare(strict_types=1);

namespace Billing;

interface MeetupsForBilling
{
    public function numberOfMeetupsActuallyHosted(
        string $organizerId,
        int $month,
        int $year
    ): int;
}
