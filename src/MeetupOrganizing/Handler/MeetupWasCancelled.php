<?php
declare(strict_types=1);

namespace MeetupOrganizing\Handler;

use MeetupOrganizing\Entity\MeetupId;

final class MeetupWasCancelled
{
    public function __construct(
        public readonly MeetupId $meetupId
    ) {
    }
}
