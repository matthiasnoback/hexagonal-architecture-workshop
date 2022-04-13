<?php
declare(strict_types=1);

namespace MeetupOrganizing\Handler;

final class MeetupWasCancelled
{
    public function __construct(
        private readonly string $meetupId,
    ) {
    }

    public function meetupId(): string
    {
        return $this->meetupId;
    }
}
