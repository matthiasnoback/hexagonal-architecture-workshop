<?php
declare(strict_types=1);

namespace Billing;

use DateTimeInterface;

final class FakeMeetupRepository implements MeetupRepository
{
    /**
     * @var array<string,int>
     */
    private array $count = [];

    public function setCount(string $organizerId, int $count): void
    {
        $this->count[$organizerId] = $count;
    }

    public function countActiveMeetups(DateTimeInterface $from, DateTimeInterface $until, string $organizerId): int
    {
        return $this->count[$organizerId] ?? throw new \OutOfBoundsException();
    }
}
