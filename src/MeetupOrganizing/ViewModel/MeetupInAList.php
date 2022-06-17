<?php
declare(strict_types=1);

namespace MeetupOrganizing\ViewModel;

final class MeetupInAList
{
    public function __construct(
        public readonly string $meetupId,
        public readonly string $name,
        public readonly string $scheduledFor,
        public readonly string $organizerId,
        public readonly int $rsvpCount
    ) {
    }

    public static function fromDatabaseRecord(array $record): self
    {
        return new self(
            $record['meetupId'],
            $record['name'],
            $record['scheduledFor'],
            $record['organizerId'],
            $record['rsvpCount'],
        );
    }
}
