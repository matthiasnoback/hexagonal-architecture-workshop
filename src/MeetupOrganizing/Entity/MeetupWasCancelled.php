<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use Shared\ExternalEvents\MeetupOrganizingMeetupWasCancelled;

final class MeetupWasCancelled
{
    public function __construct(private int $meetupId)
    {
    }

    public function meetupId(): int
    {
        return $this->meetupId;
    }

    public function asExternalEvent(): MeetupOrganizingMeetupWasCancelled
    {
        return new MeetupOrganizingMeetupWasCancelled(
            $this->meetupId
        );
    }
}
