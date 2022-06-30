<?php
declare(strict_types=1);

namespace MeetupOrganizing\ViewModel;

interface MeetupListRepository
{
    /**
     * @return list<MeetupForList>
     */
    public function listMeetups(bool $showPastMeetups): array;
}
