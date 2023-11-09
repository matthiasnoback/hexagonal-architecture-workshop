<?php

namespace MeetupOrganizing\Entity;

interface MeetupRepository
{
    public function save(Meetup $entity): void;

    public function nextIdentity(): MeetupId;
}
