<?php
declare(strict_types=1);

namespace MeetupOrganizing;

use App\Entity\UserId;
use MeetupOrganizing\Entity\ScheduledDate;

final class MeetupWasScheduled
{
    public function __construct(
        private readonly string $meetupId,
        private readonly UserId $organizerId,
        private readonly ScheduledDate $scheduledDate,
    )
    {
    }

    public function meetupId(): string
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
