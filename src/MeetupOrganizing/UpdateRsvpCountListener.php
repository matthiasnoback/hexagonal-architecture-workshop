<?php
declare(strict_types=1);

namespace MeetupOrganizing;

use MeetupOrganizing\Entity\RsvpWasCancelled;
use MeetupOrganizing\Entity\UserHasRsvpd;

final class UpdateRsvpCountListener
{
    public function __construct(private readonly MeetupRsvpCountRepository $repository)
    {
    }

    public function whenUserHasRsvpd(UserHasRsvpd $event): void
    {
        $this->repository->increaseRsvpCount($event->meetupId());
    }

    public function whenRsvpWasCancelled(RsvpWasCancelled $event): void
    {
        $this->repository->decreaseRsvpCount($event->meetupId);
    }
}
