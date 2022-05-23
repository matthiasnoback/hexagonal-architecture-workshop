<?php

declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use App\Entity\UserId;
use RuntimeException;

final class CouldNotFindRsvp extends RuntimeException
{
    public static function withMeetupAndUserId(string $meetupId, UserId $userId): self
    {
        return new self(sprintf('RSVP for meetup ID "%s" and user ID "%s" not found', $meetupId, $userId->asString()));
    }
}
