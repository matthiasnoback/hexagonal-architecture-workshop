<?php

namespace MeetupOrganizing\ViewModel;

use App\Clock;
use App\MeetupForList;
use Doctrine\DBAL\Connection;

class ListMeetupsRepositoryUsingDbal implements ListMeetupsRepository
{
    public function __construct(
        private Clock $clock,
        private Connection $connection
    )
    {

    }
    public function listMeetups(bool $showPastMeetups): array
    {
        $query = 'SELECT m.* FROM meetups m WHERE m.wasCancelled = 0';
        $parameters = [];

        if (!$showPastMeetups) {
            $query .= ' AND scheduledFor >= ?';
            $parameters[] = $this->clock->now()->format('Y-m-d H:i');
        }

        $meetups = $this->connection->fetchAllAssociative($query, $parameters);

        $meetupLists = [];
        foreach ($meetups as $meetupRecord) {
            $meetupLists[] = MeetupForList::createFromRecord($meetupRecord);
        }

        return $meetupLists;
    }
}
