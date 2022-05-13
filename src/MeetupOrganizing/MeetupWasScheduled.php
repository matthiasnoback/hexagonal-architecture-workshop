<?php
declare(strict_types=1);

namespace MeetupOrganizing;

use App\Entity\UserId;
use MeetupOrganizing\Entity\ScheduledDate;

final class MeetupWasScheduled
{
    private string $meetupId;
    private UserId $organizerId;
    private ScheduledDate $scheduledDate;

    public function __construct(
        string $meetupId,
        UserId $organizerId,
        ScheduledDate $scheduledDate,
    ) {
        $this->meetupId = $meetupId;
        $this->organizerId = $organizerId;
        $this->scheduledDate = $scheduledDate;
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
