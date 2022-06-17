<?php
declare(strict_types=1);

namespace MeetupOrganizing\ViewModel;

interface MeetupInAListRepository
{
    /**
     * @return array<MeetupInAList>
     */
    public function listMeetups(bool $showPastMeetups): array;
}
