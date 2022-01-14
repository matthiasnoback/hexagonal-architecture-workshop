<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Connection;

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
}
