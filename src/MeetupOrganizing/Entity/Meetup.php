<?php

namespace MeetupOrganizing\Entity;

use App\Entity\UserId;
use Assert\Assert;
use Assert\Assertion;

/**
 * Entity
 */
class Meetup
{
    private UserId $organizerId;

    private string $name;
    private string $description;
    private string $scheduledFor;

    private bool $wasCancelled;

    private function __construct(UserId $organizerId, string $name, string $description, string $scheduledFor, bool $wasCancelled)
    {
        Assertion::notBlank($name);
        Assertion::notBlank($description);
        Assertion::notBlank($scheduledFor);

        // checks? not sure
        $this->organizerId = $organizerId;
        $this->name = $name;
        $this->description = $description;
        $this->scheduledFor = $scheduledFor;
        $this->wasCancelled = $wasCancelled;
    }

    public static function schedule(UserId $organizerId, string $name, string $description, string $scheduledFor): self
    {
        // checks for "scheduling"
        // date should be in the future

        return new self($organizerId, $name, $description, $scheduledFor, false);
    }

    /**
     * @return array<string,string|int>
     */
    public function toRecord(): array
    {
        return [
            'organizerId' => $this->organizerId->asString(),
            'name' => $this->name,
            'description' => $this->description,
            'scheduledFor' => $this->scheduledFor,
            'wasCancelled' => (int) $this->wasCancelled,
        ];
    }
}
