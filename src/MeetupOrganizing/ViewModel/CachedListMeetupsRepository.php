<?php

namespace MeetupOrganizing\ViewModel;

class CachedListMeetupsRepository implements ListMeetupsRepository
{
    public function __construct(private ListMeetupsRepository $realRepository)
    {
    }
    public function listMeetups(bool $showPastMeetups): array
    {
        // deal with cache
        return $this->realRepository->listMeetups($showPastMeetups);
    }
}
