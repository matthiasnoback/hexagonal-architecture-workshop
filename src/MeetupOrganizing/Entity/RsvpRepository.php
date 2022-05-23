<?php

declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use App\Entity\UserId;

interface RsvpRepository
{
    public function save(Rsvp $rsvp): void;

    public function nextIdentity(): RsvpId;


    public function getByMeetupAndUserId(string $meetupId, UserId $userId): Rsvp;
}
