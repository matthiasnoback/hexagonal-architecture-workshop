<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use App\Entity\UserId;
use Assert\Assertion;
use DateTimeImmutable;

final class Meetup
{
    private DateTimeImmutable $scheduledDateTime;

    private int $meetupId;

    /**
     * @internal Only to be used by the repository
     */
    public function setMeetupId(int $meetupId): void
    {
        $this->meetupId = $meetupId;
    }

    private function __construct(
        private readonly UserId $organizerId,
        private readonly string $name,
        private readonly string $description,
        string $scheduledDateTime,
        private readonly bool $wasCancelled = false,
    )
    {
        Assertion::notEmpty($this->name, 'Name cannot be empty');
        Assertion::notEmpty($this->description, 'Description cannot be empty');

        $scheduledDateTime = DateTimeImmutable::createFromFormat(
            'Y-m-d H:i',
            $scheduledDateTime
        );
        Assertion::isInstanceOf($scheduledDateTime, DateTimeImmutable::class);
        $this->scheduledDateTime = $scheduledDateTime;
    }

    public static function schedule(
        UserId $organizerId,
        string $name,
        string $description,
        string $scheduledDateTime
    ): self {
        return new self($organizerId, $name, $description, $scheduledDateTime);
    }

    /**
     * @return array<string,string|int>
     */
    public function toArray(): array
    {
        return [
            'organizerId' => $this->organizerId->asString(),
            'name' => $this->name,
            'description' => $this->description,
            'scheduledFor' => $this->scheduledDateTime
                ->format('Y-m-d H:i'),
            'wasCancelled' => (int) $this->wasCancelled,
        ];
    }

    public function meetupId(): int
    {
        return $this->meetupId;
    }
}
