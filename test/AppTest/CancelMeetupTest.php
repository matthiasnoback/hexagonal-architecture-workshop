<?php

declare(strict_types=1);

namespace AppTest;

final class CancelMeetupTest extends AbstractBrowserTest
{
    public function testCancelMeetup(): void
    {
        $this->signUp('Organizer', 'organizer@gmail.com', 'Organizer');
        $this->login('organizer@gmail.com');

        $this->scheduleMeetup('Coding Dojo', 'Some description', '2024-10-10', '20:00');

        $this->cancelMeetup('Coding Dojo');

        $this->assertUpcomingMeetupDoesNotExist('Coding Dojo');
    }
}
