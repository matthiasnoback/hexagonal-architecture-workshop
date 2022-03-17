<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

final class MeetupWasCancelled
{
    public function __construct(private int $meetupId)
    {
    }

    public function meetupId(): int
    {
        return $this->meetupId;
    }
}
