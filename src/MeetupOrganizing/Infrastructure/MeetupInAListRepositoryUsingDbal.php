<?php
declare(strict_types=1);

namespace MeetupOrganizing\Infrastructure;

use App\Core\Time\Clock;
use Doctrine\DBAL\Connection;
use MeetupOrganizing\ViewModel\MeetupInAList;
use MeetupOrganizing\ViewModel\MeetupInAListRepository;

final class MeetupInAListRepositoryUsingDbal implements MeetupInAListRepository
{
    public function __construct(
        private readonly Connection $connection,
        private readonly Clock $clock
    ) {
    }

    public function listMeetups(bool $showPastMeetups): array
    {
        $query = 'SELECT m.* FROM meetups m WHERE m.wasCancelled = 0';
        $parameters = [];

        if (!$showPastMeetups) {
            $query .= ' AND scheduledFor >= ?';
            $parameters[] = $this->clock->whatIsTheTime()->format('Y-m-d H:i');
        }

        $meetups = $this->connection->fetchAllAssociative($query, $parameters);

        return array_map(
            [MeetupInAList::class, 'fromDatabaseRecord'],
            $meetups
        );
    }
}
