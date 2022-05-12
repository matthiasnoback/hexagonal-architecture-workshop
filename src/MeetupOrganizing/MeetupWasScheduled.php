<?php
declare(strict_types=1);

namespace MeetupOrganizing;

use App\Entity\UserId;

final class MeetupWasScheduled
{
    private string $meetupId;
    private UserId $organizerId;

    public function __construct(
        string $meetupId,
        UserId $organizerId,
    ) {
        $this->meetupId = $meetupId;
        $this->organizerId = $organizerId;
    }

    public function meetupId(): string
    {
        return $this->meetupId;
    }

    public function organizerId(): UserId
    {
        return $this->organizerId;
    }
}
