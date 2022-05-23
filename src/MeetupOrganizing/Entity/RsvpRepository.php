<?php

declare(strict_types=1);

namespace MeetupOrganizing\Entity;

interface RsvpRepository
{
    public function save(Rsvp $rsvp): void;

    public function nextIdentity(): RsvpId;

    public function getById(RsvpId $rsvpId): Rsvp;
}
