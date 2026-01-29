<?php

namespace MeetupOrganizing\Api;

interface MeetupRepository
{
    public function countMeetups(string $organizerId, int $year, int $month): int;
}
