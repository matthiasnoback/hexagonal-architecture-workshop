<?php

namespace MeetupOrganizing\Entity;

interface MeetupRepository
{
    public function nextId(): MeetupId; // a port for generating a Meetup identity

    public function save(Meetup $meetup): void;
}
