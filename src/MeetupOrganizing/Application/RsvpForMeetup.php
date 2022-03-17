<?php
declare(strict_types=1);

namespace MeetupOrganizing\Application;

use App\Entity\UserId;

final class RsvpForMeetup
{
    public function __construct(
        private int $meetupId,
        private string $userId
    ) {
    }

    public function meetupId(): int
    {
        return $this->meetupId;
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->userId);
    }
}
