<?php
declare(strict_types=1);

namespace MeetupOrganizing\Infrastructure;

use App\Entity\UserId;
use Doctrine\DBAL\Connection;
use MeetupOrganizing\Entity\CouldNotFindRsvp;
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

    public function getByMeetupAndUserId(string $meetupId, UserId $userId): Rsvp
    {
        $record = $this->connection->fetchAssociative(
            'SELECT * FROM rsvps WHERE meetupId = ? AND userId = ?',
            [$meetupId, $userId->asString()]
        );
        if ($record === false) {
            throw CouldNotFindRsvp::withMeetupAndUserId($meetupId, $userId);
        }

        $rsvp =  Rsvp::fromDatabaseRecord($record);

        $this->savedRsvpIds[$rsvp->rsvpId()->asString()] = true;

        return $rsvp;
    }

    public function getById(RsvpId $rsvpId): Rsvp
    {
        $record = $this->connection->fetchAssociative(
            'SELECT * FROM rsvps WHERE rsvpId = ?',
            [$rsvpId->asString()]
        );
        if ($record === false) {
            throw CouldNotFindRsvp::withId($rsvpId);
        }

        $rsvp =  Rsvp::fromDatabaseRecord($record);

        $this->savedRsvpIds[$rsvp->rsvpId()->asString()] = true;

        return $rsvp;
    }
}
