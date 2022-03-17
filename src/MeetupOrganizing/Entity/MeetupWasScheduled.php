<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use App\Entity\UserId;
use Shared\ExternalEvents\MeetupOrganizingMeetupWasScheduled;

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

    public function asExternalEvent(): MeetupOrganizingMeetupWasScheduled
    {
        return new MeetupOrganizingMeetupWasScheduled(
            $this->meetupId,
            $this->organizerId->asString(),
            $this->scheduledDate->asString()
        );
    }
}
