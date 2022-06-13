<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

/**
 * @design-pattern Collection-style repository
 */
interface Meetups
{
    public function nextMeetupId(): MeetupId;

    public function get(string $meetupId): Meetup;

    public function save(Meetup $meetup): void;
}
