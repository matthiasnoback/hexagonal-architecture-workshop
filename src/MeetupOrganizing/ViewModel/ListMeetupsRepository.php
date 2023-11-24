<?php

namespace MeetupOrganizing\ViewModel;

use App\MeetupForList;

interface ListMeetupsRepository
{
    /**
     * @return array<MeetupForList>
     */
    public function listMeetups(bool $showPastMeetups): array;
}
