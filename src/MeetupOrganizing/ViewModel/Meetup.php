<?php
declare(strict_types=1);

namespace MeetupOrganizing\ViewModel;

final class Meetup
{
    public function __construct(
        private readonly int $meetupId,
        private readonly string $name,
        private readonly string $scheduledFor,
    )
    {
    }

    public static function fromDatabaseRecord(array $record): self
    {
        return new self(
            $record['meetupId'],
            $record['name'],
            $record['scheduledFor'],
        );
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
