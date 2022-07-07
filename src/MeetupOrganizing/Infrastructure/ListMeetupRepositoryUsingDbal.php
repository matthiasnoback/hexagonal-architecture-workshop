<?php
declare(strict_types=1);

namespace MeetupOrganizing\Infrastructure;

use App\Clock;
use App\Mapping;
use Doctrine\DBAL\Connection;
use MeetupOrganizing\ViewModel\ListMeetupRepository;
use MeetupOrganizing\ViewModel\MeetupInList;

final class ListMeetupRepositoryUsingDbal implements ListMeetupRepository
{
    public function __construct(
        private readonly Connection $connection,
        private readonly Clock $clock,
    )
    {

    }
    public function listMeetups(bool $showPastMeetups): array
    {
        $query = 'SELECT m.* FROM meetups m WHERE m.wasCancelled = 0';
        $parameters = [];

        if (!$showPastMeetups) {
            $query .= ' AND scheduledFor >= ?';
            $parameters[] = $this->clock->getCurrentTime()->format('Y-m-d H:i');
        }

        return array_map(
            fn(array $record) => new MeetupInList(
                Mapping::getString($record, 'meetupId'),
                Mapping::getString($record, 'name'),
                Mapping::getString($record, 'numberOfAttendees'),
                Mapping::getString($record, 'scheduledFor'),
                Mapping::getString($record, 'organizerId'),
            ),
            $this->connection->fetchAllAssociative($query, $parameters)
        );
    }
}
