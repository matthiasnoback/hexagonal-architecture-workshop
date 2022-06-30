<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use App\Application;
use App\Entity\UserId;
use Assert\Assertion;
use DateTimeImmutable;

final class Meetup
{
    private UserId $organizerId;
    private string $name;
    private string $description;
    private DateTimeImmutable $scheduledFor;
    private bool $wasCancelled = false;

    private function __construct(
        UserId $organizerId,
        string $name,
        string $description,
        DateTimeImmutable $scheduledFor,
    ) {
        // pre-conditions

        $this->organizerId = $organizerId;
        $this->name = $name;
        $this->description = $description;
        $this->scheduledFor = $scheduledFor;
    }

    public static function schedule(
        UserId $organizerId,
        string $name,
        string $description,
        DateTimeImmutable $scheduledFor,
    ): self {
        Assertion::notBlank($name, 'Can not schedule a meetup without a name');
        Assertion::notBlank($description, 'Can not schedule a meetup without a description');

        // TODO check if organizer exists
        // TODO check if date is in the future

        return new self($organizerId, $name, $description, $scheduledFor);
    }

    /**
     * @return array<string,string|int|null>
     * @can-only-be-called-by(MeetupRepository)
     */
    public function toDatabaseRecord(): array
    {
        return [
            'organizerId' => $this->organizerId->asString(),
            'name' => $this->name,
            'description' => $this->description,
            'scheduledFor' => $this->scheduledFor->format(Application::DATE_TIME_FORMAT),
            'wasCancelled' => (int) $this->wasCancelled,
        ];
    }
}
