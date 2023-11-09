<?php

namespace App;

use Assert\Assertion;

class MeetupForList
{
    public function __construct(
        private string $meetupId,

        private string $name,

        private string $scheduledFor,

        private string $organizerId,
    )
    {
    }

    public static function createFromRecord(array $meetupRecord): self
    {
        Assertion::keyExists($meetupRecord, 'meetupId');

        return new self(
            $meetupRecord['meetupId'],
            self::getStringFromKey($meetupRecord, 'name'),
            $meetupRecord['scheduledFor'],
            $meetupRecord['organizerId']
        );
    }

    private static function getStringFromKey(array $data, string $key): string
    {
        Assertion::keyExists($data, $key);
        Assertion::string($data[$key]);

        return $data[$key];
    }

    public function getMeetupId(): string
    {
        return $this->meetupId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getScheduledFor(): string
    {
        return $this->scheduledFor;
    }

    public function getOrganizerId(): string
    {
        return $this->organizerId;
    }
}
