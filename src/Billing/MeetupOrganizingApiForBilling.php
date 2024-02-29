<?php
declare(strict_types=1);

namespace Billing;

interface MeetupOrganizingApiForBilling
{
    public function numberOfMeetupsActuallyHosted(
        string $organizerId,
        int $month,
        int $year
    ): int;
}
