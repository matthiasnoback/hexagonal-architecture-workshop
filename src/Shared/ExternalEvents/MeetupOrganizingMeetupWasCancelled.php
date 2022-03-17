<?php
declare(strict_types=1);

namespace Shared\ExternalEvents;

use App\Mapping;

final class MeetupOrganizingMeetupWasCancelled
{
    public const NAME = 'meetup.cancelled';

    public function __construct(
        private int $meetupId,
    ) {
    }

    public function name(): string
    {
        return self::NAME;
    }

    public function meetupId(): int
    {
        return $this->meetupId;
    }

    public function toArray(): array
    {
        return [
            'meetupId' => $this->meetupId
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(Mapping::getInt($data, 'meetupId'));
    }
}
