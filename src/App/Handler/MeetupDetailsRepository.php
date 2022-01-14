<?php

declare(strict_types=1);

namespace App\Handler;

use App\Mapping;
use Doctrine\DBAL\Connection;
use RuntimeException;

final class MeetupDetailsRepository
{
    public function __construct(
        private readonly Connection $connection
    ) {
    }

    public function getById(string $meetupId): MeetupDetails
    {
        $record = $this->connection->fetchAssociative(
            'SELECT m.*, u.name as organizerName FROM meetups m INNER JOIN users u ON m.organizerId = u.userId WHERE meetupId = ?',
            [$meetupId]
        );
        if ($record === false) {
            throw new RuntimeException('Meetup not found');
        }

        $attendeeRecords = $this->connection->fetchAllAssociative(
            'SELECT u.name FROM rsvps r INNER JOIN users u ON r.userId = u.userId WHERE r.meetupId = ?',
            [$meetupId]
        );

        return new MeetupDetails(
            Mapping::getInt($record, 'meetupId'),
            Mapping::getString($record, 'name'),
            Mapping::getString($record, 'description'),
            Mapping::getString($record, 'scheduledFor'),
            new Organizer(Mapping::getString($record, 'organizerId'), Mapping::getString($record, 'organizerName')),
            array_column($attendeeRecords, 'name')
        );
    }
}
