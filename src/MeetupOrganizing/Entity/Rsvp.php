<?php

declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use App\Entity\EventRecordingCapabilities;
use App\Entity\UserId;
use App\Mapping;
use RuntimeException;

final class Rsvp
{
    use EventRecordingCapabilities;

    private bool $wasCancelled = false;

    private function __construct(
        private readonly RsvpId $rsvpId,
        private readonly string $meetupId,
        private readonly UserId $userId
    ) {
    }

    public static function create(RsvpId $rsvpId, string $meetupId, UserId $userId): self
    {
        return new self($rsvpId, $meetupId, $userId);
    }

    public static function fromDatabaseRecord(array $record): self
    {
        $rsvp = new self(
            RsvpId::fromString(Mapping::getString($record, 'rsvpId')),
            Mapping::getString($record, 'meetupId'),
            UserId::fromString(Mapping::getString($record, 'userId')),
        );

        $rsvp->wasCancelled = (bool) Mapping::getInt($record, 'wasCancelled');

        return $rsvp;
    }

    public function rsvpId(): RsvpId
    {
        return $this->rsvpId;
    }

    public function cancel(UserId $cancelledBy): void
    {
        if (!$this->userId->equals($cancelledBy)) {
            throw new RuntimeException(sprintf('User "%s" can not cancel this RSVP', $cancelledBy->asString()));
        }

        $this->wasCancelled = true;

        $this->recordThat(
            new RsvpWasCancelled($this->rsvpId)
        );
    }

    public function meetupId(): string
    {
        return $this->meetupId;
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    /**
     * @return array<string,string|int>
     */
    public function asDatabaseRecord(): array
    {
        return [
            'rsvpId' => $this->rsvpId->asString(),
            'meetupId' => $this->meetupId,
            'userId' => $this->userId()->asString(),
            'wasCancelled' => (int) $this->wasCancelled,
        ];
    }
}
