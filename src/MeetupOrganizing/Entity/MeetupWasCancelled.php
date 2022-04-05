<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

final class MeetupWasCancelled
{
    public function __construct(
        private MeetupId $meetupId
    ) {
    }

    public function meetupId(): MeetupId
    {
        return $this->meetupId;
    }
}
