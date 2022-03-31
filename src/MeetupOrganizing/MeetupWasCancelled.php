<?php
declare(strict_types=1);

namespace MeetupOrganizing;

final class MeetupWasCancelled
{
    public function __construct(
        private string $meetupId,
    ) {
    }

    public function meetupId(): string
    {
        return $this->meetupId;
    }
}
