<?php
declare(strict_types=1);

namespace MeetupOrganizing\ViewModel;

use App\Mapping;

final class MeetupForList
{
    public function __construct(
        public readonly int $meetupId,
        public readonly string $name,
        public readonly string $organizerId,
        public readonly string $scheduledFor,
    )
    {
    }

    public static function fromRecord(array $record): self
    {
        return new self(
            Mapping::getInt($record, 'meetupId'),
            $record['name'],
            $record['organizerId'],
            $record['scheduledFor'],
        );
    }
}
