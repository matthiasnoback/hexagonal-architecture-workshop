<?php
declare(strict_types=1);

namespace Billing;

final class MeetupsForTesting implements Meetups
{
    private int $count;

    public function setScheduledMeetupsCount(int $count): void
    {
        $this->count = $count;
    }

    public function countScheduledMeetupsFor(int $year, int $month, string $organizerId): int
    {
        return $this->count;
    }
}
