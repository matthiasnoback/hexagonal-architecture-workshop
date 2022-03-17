<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use App\Entity\UserId;

final class MeetupWasScheduled
{
    public function __construct(
        private int $meetupId,
        private UserId $organizerId,
        private ScheduledDate $scheduledDate,
    ) {
    }

    public function meetupId(): int
    {
        return $this->meetupId;
    }

    public function organizerId(): UserId
    {
        return $this->organizerId;
    }

    public function scheduledDate(): ScheduledDate
    {
        return $this->scheduledDate;
    }
}
