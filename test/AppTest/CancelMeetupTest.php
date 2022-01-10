<?php

declare(strict_types=1);

namespace AppTest;

final class CancelMeetupTest extends AbstractBrowserTest
{
    public function testCancelMeetup(): void
    {
        $this->iAmLoggedInAsOrganizer();

        $this->iScheduleAMeetup('Coding Dojo', 'Some description', '2024-10-10', '20:00');

        $this->iCancelMeetup('Coding Dojo');

        $this->iShouldNotSeeUpcomingMeetup('Coding Dojo');
    }
}
