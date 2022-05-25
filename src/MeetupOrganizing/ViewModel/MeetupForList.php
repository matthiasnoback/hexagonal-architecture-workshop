<?php
declare(strict_types=1);

namespace MeetupOrganizing\ViewModel;

use App\Mapping;

final class MeetupForList
{
    public function __construct(
        public string $meetupId,
        public string $name,
        public int $numberOfAttendees,
        public string $scheduledFor,
        public string $organizerId,
    ) {
    }

    public static function fromDatabaseRecord(array $record): self
    {
        return new self(
            Mapping::getString($record, 'meetupId'),
            Mapping::getString($record, 'name'),
            Mapping::getInt($record, 'numberOfAttendees'),
            Mapping::getString($record, 'scheduledFor'),
            Mapping::getString($record, 'organizerId'),
        );
    }

    public function isOrganizedBy(?string $userId): bool
    {
        if ($userId === null) {
            return false;
        }

        return $userId === $this->organizerId;
    }
}
