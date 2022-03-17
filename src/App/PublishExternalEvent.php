<?php
declare(strict_types=1);

namespace App;

use App\Entity\UserHasSignedUp;
use MeetupOrganizing\Entity\MeetupWasCancelled;
use MeetupOrganizing\Entity\MeetupWasScheduled;

final class PublishExternalEvent
{
    public function __construct(
        private readonly ExternalEventPublisher $publisher
    ) {
    }

    public function whenUserHasSignedUp(UserHasSignedUp $event): void
    {
        $this->publisher->publish(
            'user.signed_up',
            [
                'id' => $event->userId()->asString(),
                'name' => $event->name(),
                'type' => $event->userType()->value
            ]
        );
    }

    public function whenMeetupWasScheduled(MeetupWasScheduled $event): void
    {
        $this->publisher->publish(
            'meetup.scheduled',
            [
                'meetupId' => $event->meetupId(),
                'organizerId' => $event->organizerId()->asString(),
                'scheduledDate' => $event->scheduledDate()->asString()
            ]
        );
    }

    public function whenMeetupWasCancelled(MeetupWasCancelled $event): void
    {
        $this->publisher->publish(
            'meetup.cancelled',
            [
                'meetupId' => $event->meetupId(),
            ]
        );
    }
}
