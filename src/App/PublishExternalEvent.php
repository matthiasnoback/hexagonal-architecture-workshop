<?php
declare(strict_types=1);

namespace App;

use App\Entity\UserHasSignedUp;
use DateTimeImmutable;
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

    public function whenMeetupWasScheduledByOrganizer(
        MeetupWasScheduledByOrganizer $event
    ): void {
        $this->publisher->publish(
            'meetup_organizing.meetup_was_scheduled_by_organizer',
            [
                'id' => $event->meetupId,
                'organizerId' => $event->userId,
                'scheduledDate' => $event->scheduledDate->toDateTimeImmutable()->format(
                    DateTimeImmutable::RFC3339
                )
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
