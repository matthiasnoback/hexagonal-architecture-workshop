<?php
declare(strict_types=1);

namespace App;

final class Meetup
{
    public function __construct(
        public readonly string $meetupId,
        public readonly string $name,
        public readonly string $organizerId,
        public readonly string $scheduledFor,
    )
    {
    }
}
