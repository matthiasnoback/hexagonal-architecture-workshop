<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

final class CouldNotRescheduleMeetup extends \RuntimeException
{
    public static function becauseTheMeetupWasCancelled(MeetupId $meetupId): self
    {
        return new self(
            'Can\'t reschedule the meetup with ID ' . $meetupId->asString() . ' because it was already cancelled'
        );
    }

    public static function becauseTheMeetupAlreadyTookPlace(MeetupId $meetupId): self
    {
        return new self(
            'Can\'t reschedule the meetup with ID ' . $meetupId->asString() . ' because it already took place'
        );
    }
}
