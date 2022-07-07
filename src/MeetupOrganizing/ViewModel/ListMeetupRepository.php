<?php
declare(strict_types=1);

namespace MeetupOrganizing\ViewModel;

interface ListMeetupRepository
{
    /**
     * @return array<MeetupInList>
     * @port-type left
     * @port-type right
     */
    public function listMeetups(bool $showPastMeetups): array;
}
