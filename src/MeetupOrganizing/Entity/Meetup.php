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

    private function __construct(
        string $organizerId,
        string $name,
        string $description,
        string $scheduledFor,
        bool   $wasCancelled
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

        $this->organizerId = $organizerId;
        $this->name = $name;
        $this->description = $description;
        $this->scheduledFor = $scheduledFor;
        $this->wasCancelled = $wasCancelled;
    }

    public static function schedule(string $organizerId,
                                    string $name,
                                    string $description,
                                    string $scheduledFor): self
    {
        return new self(
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
            'organizerId' => $this->organizerId,
            'name' => $this->name,
            'description' => $this->description,
            'scheduledFor' => $this->scheduledFor,
            'wasCancelled' => (int) $this->wasCancelled,
        ];
    }
}
