<?php
declare(strict_types=1);

namespace MeetupOrganizing\Handler;

use App\Entity\UserId;

final class MeetupWasCancelled
{
    public function __construct(
        private UserId $organizerId,
        private string $meetupId,
    ) {
    }

    public function organizerId(): UserId
    {
        return $this->organizerId;
    }

    public function meetupId(): string
    {
        return $this->meetupId;
    }
}
