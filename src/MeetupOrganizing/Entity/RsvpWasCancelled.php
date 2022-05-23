<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

final class RsvpWasCancelled
{
    public function __construct(public readonly RsvpId $rsvpId)
    {
    }
}
