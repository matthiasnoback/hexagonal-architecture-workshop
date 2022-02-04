<?php
declare(strict_types=1);

namespace Billing;

final class MeetupsForTesting implements Meetups
{
    private array $counts = [];

    public function setScheduledMeetupsCount(
        int $year, int $month, string $organizerId, int $count
    ): void
    {
        $this->counts[$year][$month][$organizerId] = $count;
    }

    public function countScheduledMeetupsFor(int $year, int $month, string $organizerId): int
    {
        return $this->counts[$year][$month][$organizerId]
            ?? throw new \RuntimeException('First call setScheduledMeetupsCount');
    }
}
