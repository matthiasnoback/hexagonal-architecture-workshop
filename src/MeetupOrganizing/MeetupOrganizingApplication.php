<?php
declare(strict_types=1);

namespace MeetupOrganizing;

final class MeetupOrganizingApplication implements MeetupOrganizingApplicationInterface
{
    public function __construct(
        private readonly MeetupRepositoryUsingOurOwnDatabase $meetupRepository,
    )
    {
    }

    public function getNumberOfMeetups(string $organizerId, int $year, int $month,): int
    {
        return $this->meetupRepository->getNumberOfMeetups(
            $organizerId,
            $year,
            $month,
        );
    }
}
