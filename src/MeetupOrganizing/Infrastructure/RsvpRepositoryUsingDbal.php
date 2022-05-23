<?php
declare(strict_types=1);

namespace MeetupOrganizing\Infrastructure;

use Doctrine\DBAL\Connection;
use MeetupOrganizing\Entity\Rsvp;
use MeetupOrganizing\Entity\RsvpId;
use MeetupOrganizing\Entity\RsvpRepository;
use Ramsey\Uuid\Uuid;
use RuntimeException;

final class RsvpRepositoryUsingDbal implements RsvpRepository
{
    /**
     * @var array<string,true>
     */
    private array $savedRsvpIds = [];

    public function __construct(
        private readonly Connection $connection
    ) {
    }

    public function save(Rsvp $rsvp): void
    {
        if (isset($this->savedRsvpIds[$rsvp->rsvpId()->asString()])) {
            $this->connection->update(
                'rsvps',
                $rsvp->asDatabaseRecord(),
                [
                    'rsvpId' => $rsvp->rsvpId()->asString()
                ]
            );
        } else {
            $this->connection->insert(
                'rsvps',
                $rsvp->asDatabaseRecord(),
            );
            $this->savedRsvpIds[$rsvp->rsvpId()->asString()] = true;
        }
    }

    public function nextIdentity(): RsvpId
    {
        return RsvpId::fromString(Uuid::uuid4()->toString());
    }

    public function getById(RsvpId $rsvpId): Rsvp
    {
        $record = $this->connection->fetchAssociative(
            'SELECT * FROM rsvps WHERE rsvpId = ?',
            [$rsvpId->asString()]
        );
        if ($record === false) {
            throw new RuntimeException(sprintf('RSVP with ID "%s" found', $rsvpId->asString()));
        }

        $this->savedRsvpIds[$rsvpId->asString()] = true;

        return Rsvp::fromDatabaseRecord($record);
    }
}
