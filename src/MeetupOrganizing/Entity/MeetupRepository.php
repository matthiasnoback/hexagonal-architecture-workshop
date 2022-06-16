<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

/**
 * @object-type write model repository
 */
interface MeetupRepository
{
    public function save(Meetup $meetup): int;
}
