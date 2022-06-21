<?php
declare(strict_types=1);

namespace MeetupOrganizing\ViewModel;

interface MeetupListRepository
{
    /**
     * @return array<MeetupSummaryForList>
     */
    public function listMeetups(bool $showPastMeetups): array;
}
