<?php

namespace Billing;

interface Meetup
{
    public function numberOfMeetups(int $year, int $month, string $organizerId): int;
}
