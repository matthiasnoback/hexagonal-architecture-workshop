<?php
declare(strict_types=1);

namespace Billing\Handler;

interface MeetupRepository
{
    public function getCount(string $organizerId, int $year, int $month): int;
}
