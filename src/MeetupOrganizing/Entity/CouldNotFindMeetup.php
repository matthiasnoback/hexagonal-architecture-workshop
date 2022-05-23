<?php

declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use RuntimeException;

final class CouldNotFindMeetup extends RuntimeException
{
    public static function withId(string $meetupId): self
    {
        return new self($meetupId);
    }
}
