<?php
declare(strict_types=1);

namespace MeetupOrganizing\Infrastructure;

use App\CurrentTimeAccessor;
use App\Mapping;
use Doctrine\DBAL\Connection;
use MeetupOrganizing\ViewModel\MeetupForList;
use MeetupOrganizing\ViewModel\MeetupListRepository;

final class MeetupListRepositoryForDbal implements MeetupListRepository
{
    public function __construct(
        private readonly Connection $connection,
        private readonly CurrentTimeAccessor $currentTimeAccessor,
    )
    {
    }

    public function listMeetups(bool $showPastMeetups): array
    {
        $query = 'SELECT m.* FROM meetups m WHERE m.wasCancelled = 0';
        $parameters = [];

        if (!$showPastMeetups) {
            $query .= ' AND scheduledFor >= ?';
            $parameters[] = $this->currentTimeAccessor->getCurrentTime()->format('Y-m-d H:i');
        }

        $meetups = $this->connection->fetchAllAssociative($query, $parameters);

        return array_map(
            fn (array $record) => new MeetupForList(
                Mapping::getString($record, 'meetupId'),
                Mapping::getString($record, 'name'),
                Mapping::getString($record, 'scheduledFor'),
                Mapping::getString($record, 'organizerId'),
                Mapping::getInt($record, 'attendees'),
            ),
            $meetups
        );
    }
}
