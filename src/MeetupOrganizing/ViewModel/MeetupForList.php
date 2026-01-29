<?php
declare(strict_types=1);

namespace MeetupOrganizing\ViewModel;

use App\Mapping;

final class MeetupForList
{
    private function __construct(
        public readonly string $meetupId,
        public readonly string $name,
        public readonly string $scheduledFor,
        public readonly string $organizerId,
    )
    {
    }

    public static function fromDatabaseRecord(array $record): self
    {
        return new self(Mapping::getString($record, 'meetupId'), Mapping::getString($record, 'name'), Mapping::getString($record, 'scheduledFor'), Mapping::getString($record, 'organizerId'));
    }
}
