<?php
declare(strict_types=1);

namespace App;

final class ScheduleMeetup
{
    public function __construct(
        public readonly string $organizerId,
        public readonly string $name,
        public readonly string $description,
        public readonly string $scheduledFor,
    ) {
    }
}
