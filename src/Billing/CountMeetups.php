<?php

namespace Billing;

interface CountMeetups
{
    public function forOrganizer(int $year, int $month, string $organizerId): int;
}
