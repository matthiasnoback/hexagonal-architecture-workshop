<?php

declare(strict_types=1);

namespace AppTest;

final class ScheduleMeetupTest extends AbstractBrowserTest
{
    public function testScheduleMeetup(): void
    {
        $this->signUp('Organizer', 'organizer@gmail.com', 'Organizer');
        $this->login('organizer@gmail.com');

        $this->scheduleMeetup('Coding Dojo', 'Some description', '2024-10-10', '20:00');

        $this->flashMessagesShouldContain('Your meetup was scheduled successfully');

        $this->assertUpcomingMeetupExists('Coding Dojo');
    }
}
