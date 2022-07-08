<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

interface MeetupRepository
{
    public function save(Meetup $meetup): void;

    public function nextIdentity(): MeetupId;

    /**
     * @throws CouldNotFindMeetup
     */
    public function getById(MeetupId $meetupId): Meetup;
}
