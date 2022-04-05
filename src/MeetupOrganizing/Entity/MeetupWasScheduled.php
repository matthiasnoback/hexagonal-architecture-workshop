<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use App\Entity\UserId;
use DateTimeImmutable;

final class MeetupWasScheduled
{
    public function __construct(
        private readonly UserId $organizerId,
        private readonly MeetupId $meetupId,
        private readonly DateTimeImmutable $scheduledDate,
    ) {
    }

    public function organizerId(): UserId
    {
        return $this->organizerId;
    }

    public function meetupId(): MeetupId
    {
        return $this->meetupId;
    }

    public function scheduledDate(): DateTimeImmutable
    {
        return $this->scheduledDate;
    }
}
