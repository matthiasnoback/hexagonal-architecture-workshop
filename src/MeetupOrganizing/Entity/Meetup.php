<?php

namespace MeetupOrganizing\Entity;

use Assert\Assertion;

final class Meetup
{
    private string $organizerId;
    private string $name;
    private string $description;
    private string $scheduledFor;
    private bool $wasCancelled;
    private MeetupId $meetupId;

    private function __construct(
        MeetupId $meetupId,
        string   $organizerId,
        string   $name,
        string   $description,
        string   $scheduledFor,
        bool     $wasCancelled
    )
    {
        if ($organizerId === '') {
            throw new \InvalidArgumentException();
        }
        if ($name === '') {
            throw new \InvalidArgumentException();
        }
        if ($description === '') {
            throw new \InvalidArgumentException();
        }
        if ($scheduledFor === '') {
            throw new \InvalidArgumentException();
        }

        $this->meetupId = $meetupId;
        $this->organizerId = $organizerId;
        $this->name = $name;
        $this->description = $description;
        $this->scheduledFor = $scheduledFor;
        $this->wasCancelled = $wasCancelled;
    }

    public static function schedule(
        MeetupId $meetupId,
        string   $organizerId,
        string   $name,
        string   $description,
        string   $scheduledFor
    ): self
    {
        return new self(
            $meetupId,
            $organizerId,
            $name,
            $description,
            $scheduledFor,
            false
        );
    }

    public function asRecord(): array
    {
        return [
            'meetupId' => $this->meetupId->asString(),
            'organizerId' => $this->organizerId,
            'name' => $this->name,
            'description' => $this->description,
            'scheduledFor' => $this->scheduledFor,
            'wasCancelled' => (int)$this->wasCancelled,
        ];
    }

    public function meetupId(): MeetupId
    {
        return $this->meetupId;
    }
}
