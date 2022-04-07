<?php
declare(strict_types=1);

namespace MeetupOrganizing;

use App\Entity\UserId;
use MeetupOrganizing\Entity\MeetupId;
use MeetupOrganizing\Entity\ScheduledDate;

final class MeetupWasScheduled
{
    public function __construct(
        public readonly MeetupId $meetupId,
        public readonly UserId $organizerId,
        public readonly ScheduledDate $scheduledFor,
    ) {
    }
}
