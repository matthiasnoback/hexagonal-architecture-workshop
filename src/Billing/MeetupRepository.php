<?php
declare(strict_types=1);

namespace Billing;

use DateTimeInterface;

interface MeetupRepository
{
    public function countActiveMeetups(DateTimeInterface $from, DateTimeInterface $until, string $organizerId): int;
}
