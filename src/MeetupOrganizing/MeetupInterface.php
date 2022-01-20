<?php
declare(strict_types=1);

namespace MeetupOrganizing;

interface MeetupInterface
{
    public function countMeetups(int $year, int $month, string $organizerId): int;
}
