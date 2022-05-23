<?php

declare(strict_types=1);

namespace MeetupOrganizing\ViewModel;

use App\Mapping;
use Doctrine\DBAL\Connection;
use MeetupOrganizing\Entity\Answer;
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

        $rsvpRecords = $this->connection->fetchAllAssociative(
            'SELECT r.rsvpId, r.userId, u.name as userName FROM rsvps r INNER JOIN users u ON r.userId = u.userId WHERE r.meetupId = ? AND r.answer = ?',
            [$meetupId, Answer::Yes->value]
        );

        return new MeetupDetails(
            Mapping::getString($record, 'meetupId'),
            Mapping::getString($record, 'name'),
            Mapping::getString($record, 'description'),
            Mapping::getString($record, 'scheduledFor'),
            new Organizer(Mapping::getString($record, 'organizerId'), Mapping::getString($record, 'organizerName')),
            array_combine(array_column($rsvpRecords, 'userId'), array_column($rsvpRecords, 'userName'),),
        );
    }
}
