<?php

declare(strict_types=1);

namespace App\Entity;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class Rsvp
{
    private function __construct(
        private readonly UuidInterface $rsvpId,
        private readonly string $meetupId,
        private readonly UserId $userId
    ) {
    }

    public static function create(string $meetupId, UserId $userId): self
    {
        return new self(Uuid::uuid4(), $meetupId, $userId);
    }

    public static function fromDatabaseRecord(array $record): self
    {
        return new self(
            Uuid::fromString($record['rsvpId']),
            $record['meetupId'],
            UserId::fromString($record['userId'])
        );
    }

    public function rsvpId(): UuidInterface
    {
        return $this->rsvpId;
    }

    public function meetupId(): string
    {
        return $this->meetupId;
    }

    public function userId(): UserId
    {
        return $this->userId;
    }
}
