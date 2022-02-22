<?php
declare(strict_types=1);

namespace MeetupOrganizing;

interface MeetupOrganizingApplicationInterface
{
    public function getNumberOfMeetups(string $organizerId,
        int $year,
        int $month,
    ): int;
}
