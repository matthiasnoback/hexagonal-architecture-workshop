<?php

namespace MeetupOrganizing\Entity;

interface MeetupRepository
{
    public function save(Meetup $entity): int;
}
