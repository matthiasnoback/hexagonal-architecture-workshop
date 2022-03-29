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
        $dto = new \Shared\DTOs\MeetupOrganizing\MeetupWasScheduledByOrganizer(
            $event->meetupId,
            $event->userId,
            $event->scheduledDate->toDateTimeImmutable()
        );
        $this->publisher->publish(
            'meetup_organizing.meetup_was_scheduled_by_organizer',
            $dto->toArray()
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
