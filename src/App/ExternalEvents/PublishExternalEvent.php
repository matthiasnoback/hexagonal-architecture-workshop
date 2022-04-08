<?php
declare(strict_types=1);

namespace App\ExternalEvents;

use App\Entity\UserHasSignedUp;
use MeetupOrganizing\Handler\MeetupWasCancelled;
use MeetupOrganizing\MeetupWasScheduled;

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
        $dto = new \Shared\MeetupWasScheduled(
            $event->meetupId->asString(),
            $event->organizerId->asString(),
            $event->scheduledFor->toDateTimeImmutable(),
        );

        $this->publisher->publish(
            $dto::EVENT_TYPE,
            $dto->toJsonSerializableArray(),
        );
    }

    public function whenMeetupWasCancelled(
        MeetupWasCancelled $event
    ): void {
        $this->publisher->publish(
            'public.meetup_organizing.meetup_was_cancelled',
            [
                'meetupId' => $event->meetupId->asString(),
            ]
        );
    }
}
