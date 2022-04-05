<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use App\Entity\UserId;

final class MeetupWasScheduled
{
    public function __construct(
        private readonly UserId $organizerId,
        private readonly MeetupId $meetupId,
    ) {
    }

    public function organizerId(): UserId
    {
        return $this->organizerId;
    }

    public function meetupId(): MeetupId
    {
        return $this->meetupId;
    }
}
