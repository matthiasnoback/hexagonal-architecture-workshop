<?php

namespace App;

/**
 * DTO Data Transfer Object
 * [
 * 'organizerId' => '',
 * ...
 * ]
 */
class ScheduleMeetup
{
    private readonly string $organizerId;
    private readonly string $name;
    private readonly string $description;
    private readonly string $scheduledFor;

    public function __construct(string $organizerId,
                                string $name,
                                string $description,
                                string $scheduledFor
    )
    {
        $this->organizerId = $organizerId;
        $this->name = $name;
        $this->description = $description;
        $this->scheduledFor = $scheduledFor;
    }

    public function getOrganizerId(): string
    {
        return $this->organizerId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getScheduledFor(): string
    {
        // TODO return value object
        return $this->scheduledFor;
    }
}
