<?php

declare(strict_types=1);

namespace App\Entity;

use Assert\Assert;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;

final class RsvpRepository
{
    public function __construct(
        private readonly Connection $connection
    ) {
    }

    public function save(Rsvp $rsvp): void
    {
        $this->connection->insert(
            'rsvps',
            [
                'rsvpId' => $rsvp->rsvpId()
                    ->toString(),
                'meetupId' => $rsvp->meetupId(),
                'userId' => $rsvp->userId()
                    ->asString(),
            ]
        );
    }

    /**
     * @return array<Rsvp>
     */
    public function getByMeetupId(string $meetupId): array
    {
        $statement = $this->connection
            ->createQueryBuilder()
            ->select('*')
            ->from('rsvps')
            ->where('meetupId = :meetupId')
            ->setParameter('meetupId', $meetupId)
            ->execute();

        Assert::that($statement)->isInstanceOf(Statement::class);
        $records = $statement->fetchAllAssociative();

        return array_map(fn (array $record) => Rsvp::fromDatabaseRecord($record), $records);
    }
}
