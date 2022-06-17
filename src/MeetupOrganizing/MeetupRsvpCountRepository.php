<?php
declare(strict_types=1);

namespace MeetupOrganizing;

use MeetupOrganizing\Entity\MeetupId;

interface MeetupRsvpCountRepository
{
    public function increaseRsvpCount(MeetupId $meetupId): void;

    public function decreaseRsvpCount(MeetupId $meetupId): void;
}
