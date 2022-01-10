<?php

declare(strict_types=1);

namespace App\Entity;

use Ramsey\Uuid\UuidInterface;

final class UserHasRsvpd
{
    public function __construct(
        private readonly string $meetupId,
        private readonly UserId $userId,
        private readonly UuidInterface $rsvpId
    ) {
    }

    public function meetupId(): string
    {
        return $this->meetupId;
    }

    public function rsvpId(): UuidInterface
    {
        return $this->rsvpId;
    }

    public function userId(): UserId
    {
        return $this->userId;
    }
}
