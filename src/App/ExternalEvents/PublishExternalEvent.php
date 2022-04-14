<?php
declare(strict_types=1);

namespace App\ExternalEvents;

use App\Entity\UserHasSignedUp;
use DateTimeInterface;
use MeetupOrganizing\Handler\MeetupWasCancelled;
use MeetupOrganizing\Handler\MeetupWasScheduled;
use MeetupOrganizingPublished\Event\MeetupWasScheduledDto;

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
        $dto = new MeetupWasScheduledDto(
            $event->meetupId(),
            $event->organizerId()->asString(),
            $event->scheduledDate()
                ->toDateTimeImmutable()->format(
                    DateTimeInterface::ATOM,
                )
        );

        $this->publisher->publish(
            $dto->eventName(),
            $dto->eventData(),
        );
    }

    public function whenMeetupWasCancelled(MeetupWasCancelled $event): void
    {
        $this->publisher->publish(
            'meetup_organizing.public.meetup.meetup_was_cancelled',
            [
                'meetupId' => $event->meetupId(),
            ]
        );
    }
}
