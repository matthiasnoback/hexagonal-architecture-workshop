<?php
declare(strict_types=1);

namespace App;

use App\Entity\UserHasSignedUp;
use MeetupOrganizing\MeetupWasCancelled;
use MeetupOrganizing\MeetupWasScheduled;
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
        // convert to external event
        $dto = new MeetupWasScheduledData(
            $event->meetupId(),
            $event->organizerId()->asString(),
            $event->scheduledDate()->asString()
        );

        $this->publisher->publish(
            MeetupWasScheduledData::NAME,
            $dto->toEventData()
        );
    }

    public function whenMeetupWasCancelled(MeetupWasCancelled $event): void
    {
        $this->publisher->publish(
            'meetup_organizing.meetup.cancelled',
            [
                'meetupId' => $event->meetupId(),
            ]
        );
    }
}
