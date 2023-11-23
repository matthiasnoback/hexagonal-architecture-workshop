<?php

namespace MeetupOrganizing\Entity;

interface MeetupRepository
{
    public function save(Meetup $meetup): int;
}
