<?php
declare(strict_types=1);

namespace MeetupOrganizing\Handler;

use App\Entity\UserId;

final class MeetupWasScheduled
{
    public function __construct(
        private readonly string $meetupId,
        private readonly UserId $organizerId,
    ) {
    }

    public function meetupId(): string
    {
        return $this->meetupId;
    }

    public function organizerId(): UserId
    {
        return $this->organizerId;
    }
}
