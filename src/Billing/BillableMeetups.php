<?php
declare(strict_types=1);

namespace Billing;

interface BillableMeetups
{
    public function howManyBillableMeetupsDoesThisOrganizerHaveInTheGivenMonth(
        string $organizerId,
        int $year,
        int $month,
    ): int;
}
