<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

/**
 * @object-type write model repository
 */
interface MeetupRepository
{
    public function save(Meetup $meetup): void;

    public function nextId(): MeetupId;

    public function getById(MeetupId $meetupId): Meetup;
}
