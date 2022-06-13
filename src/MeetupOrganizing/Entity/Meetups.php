<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

/**
 * @design-pattern Collection-style repository
 */
interface Meetups
{
    public function add(Meetup $meetup): void;
}
