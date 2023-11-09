<?php

namespace MeetupOrganizing\Entity;

interface MeetupRepository
{
    public function nextIdentity(): MeetupId;

    public function save(Meetup $entity): void;

    public function getById(MeetupId $meetupId): Meetup;
}
