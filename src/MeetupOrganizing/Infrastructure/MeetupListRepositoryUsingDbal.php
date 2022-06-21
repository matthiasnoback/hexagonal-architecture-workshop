<?php
declare(strict_types=1);

namespace MeetupOrganizing\Infrastructure;

use App\Mapping;
use App\Time\Clock;
use Doctrine\DBAL\Connection;
use MeetupOrganizing\ViewModel\MeetupListRepository;
use MeetupOrganizing\ViewModel\MeetupSummaryForList;

final class MeetupListRepositoryUsingDbal implements MeetupListRepository
{
    public function __construct(
        private readonly Clock $clock,
        private readonly Connection $connection,
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

        $meetups = $this->connection->fetchAllAssociative($query, $parameters);

        return array_map(
            fn (array $record) => new MeetupSummaryForList(
                Mapping::getString($record, 'meetupId'),
                Mapping::getString($record, 'name'),
                Mapping::getString($record, 'scheduledFor'),
                Mapping::getString($record, 'organizerId'),
                Mapping::getInt($record, 'numberOfAttendees'),
            ),
            $meetups
        );
    }
}
