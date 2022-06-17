<?php

declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use App\Entity\UserId;

final class UserHasRsvpd
{
    public function __construct(
        private readonly MeetupId $meetupId,
        private readonly UserId $userId,
        private readonly RsvpId $rsvpId
    ) {
    }

    public function meetupId(): MeetupId
    {
        return $this->meetupId;
    }

    public function rsvpId(): RsvpId
    {
        return $this->rsvpId;
    }

    public function userId(): UserId
    {
        return $this->userId;
    }
}
