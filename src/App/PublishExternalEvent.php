<?php
declare(strict_types=1);

namespace App;

use App\Entity\UserHasSignedUp;
use MeetupOrganizing\Event\MeetupWasCancelled;
use MeetupOrganizing\Event\MeetupWasScheduledByOrganizer;

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

    public function whenMeetupWasCancelled(MeetupWasCancelled $event): void
    {
        $this->publisher->publish(
            'meetup_organizing.meetup_was_cancelled',
            [
                'id' => $event->meetupId,
            ]
        );
    }
}
