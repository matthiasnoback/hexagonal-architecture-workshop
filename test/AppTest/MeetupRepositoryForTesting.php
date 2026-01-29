<?php

namespace AppTest;

use MeetupOrganizing\Api\MeetupRepository;

class MeetupRepositoryForTesting implements MeetupRepository
{
    /**
     * @var array<mixed>
     */
    private array $data;

    public function countMeetups(string $organizerId, int $year, int $month): int
    {
        return $this->data[$organizerId][$year][$month] ?? throw new \RuntimeException();
    }

    public function setMeetupCount(string $organizerId, int $year, int $month, int $count): void
    {
        $this->data[$organizerId][$year][$month] = $count;
    }
}
