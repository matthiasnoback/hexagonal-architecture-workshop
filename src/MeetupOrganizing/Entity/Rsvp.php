<?php

declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use App\Entity\EventRecordingCapabilities;
use App\Entity\UserId;
use App\Mapping;

final class Rsvp
{
    use EventRecordingCapabilities;

    private function __construct(
        private readonly RsvpId $rsvpId,
        private readonly string $meetupId,
        private readonly UserId $userId,
        private Answer $answer,
    ) {
    }

    public static function forMeetup(RsvpId $rsvpId, string $meetupId, UserId $userId): self
    {
        $rsvp = new self($rsvpId, $meetupId, $userId, Answer::Unknown);

        $rsvp->yes();

        return $rsvp;
    }

    public function yes(): void
    {
        if ($this->answer !== Answer::Yes) {
            $this->answer = Answer::Yes;

            $this->recordThat(new UserHasRsvpd(
                MeetupId::fromString($this->meetupId), $this->userId, $this->rsvpId));
        }
    }

    public function no(): void
    {
        if ($this->answer !== Answer::No) {
            $this->answer = Answer::No;

            $this->recordThat(new RsvpWasCancelled($this->rsvpId, MeetupId::fromString($this->meetupId)));
        }
    }

    public static function fromDatabaseRecord(array $record): self
    {
        return new self(
            RsvpId::fromString(Mapping::getString($record, 'rsvpId')),
            Mapping::getString($record, 'meetupId'),
            UserId::fromString(Mapping::getString($record, 'userId')),
            Answer::from(Mapping::getString($record, 'answer')),
        );
    }

    public function rsvpId(): RsvpId
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

    /**
     * @return array<string,string|int>
     */
    public function asDatabaseRecord(): array
    {
        return [
            'rsvpId' => $this->rsvpId->asString(),
            'meetupId' => $this->meetupId,
            'userId' => $this->userId()
                ->asString(),
            'answer' => $this->answer->value,
        ];
    }
}
