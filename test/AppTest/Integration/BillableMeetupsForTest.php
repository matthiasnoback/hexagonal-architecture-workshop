<?php
declare(strict_types=1);

namespace AppTest\Integration;

use Billing\BillableMeetups;

final class BillableMeetupsForTest implements BillableMeetups
{
    public function howManyBillableMeetupsDoesThisOrganizerHaveInTheGivenMonth(
        string $organizerId,
        int $year,
        int $month,
    ): int {
        return 2;
    }
}
