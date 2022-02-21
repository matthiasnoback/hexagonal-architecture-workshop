<?php
declare(strict_types=1);

namespace MeetupOrganizing\ViewModel;

final class MeetupSummary
{
    public function __construct(
        private readonly int $meetupId,
        private readonly string $name,
        private readonly string $scheduledFor,
    )
    {
    }

    public function meetupId(): int
    {
        return $this->meetupId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function scheduledFor(): string
    {
        return $this->scheduledFor;
    }
}
