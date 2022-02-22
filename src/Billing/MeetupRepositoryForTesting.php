<?php
declare(strict_types=1);

namespace Billing;

final class MeetupRepositoryForTesting implements MeetupRepository
{
    public function getNumberOfMeetups(string $organizerId, int $year, int $month): int
    {
        return 2;
    }
}
