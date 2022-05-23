<?php
declare(strict_types=1);

namespace MeetupOrganizing\ViewModel;

final class Rsvp
{
    public function __construct(
        public readonly string $rsvpId,
        public readonly string $userId,
        public readonly string $userName,
    ){
    }
}
