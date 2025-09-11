<?php

declare(strict_types=1);

namespace MeetupOrganizing\ViewModel;

final class MeetupListElement
{
    public function __construct(
        public readonly string $meetupId,
        public readonly string $scheduledFor,
        public readonly string $organizerId,
        public readonly string $name,
    ) {
    }
}
