<?php
declare(strict_types=1);

namespace App;

use App\Entity\UserHasSignedUp;
use MeetupOrganizing\Entity\MeetupWasCancelled;
use MeetupOrganizing\Entity\MeetupWasScheduled;
use Shared\MeetupWasScheduledData;

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
        $eventDto = new MeetupWasScheduledData(
            $event->organizerId()->asString(),
            $event->meetupId()->asString(),
            $event->scheduledDate()
        );
        $this->publisher->publishEvent($eventDto);
    }

    public function whenMeetupWasCancelled(MeetupWasCancelled $event): void
    {
        $this->publisher->publish(
            'meetup.cancelled',
            [
                'meetupId' => $event->meetupId()->asString(),
            ]
        );
    }
}
