<?php

declare(strict_types=1);

namespace AppTest;

final class RescheduleMeetupTest extends AbstractBrowserTest
{
    public function testRescheduleMeetup(): void
    {
        $this->signUp('Organizer', 'organizer@gmail.com', 'Organizer');
        $this->login('organizer@gmail.com');

        $this->scheduleMeetup('Coding Dojo', 'Some description', '2024-10-10', '20:00');

        $this->rescheduleMeetup('Coding Dojo', '2026-04-27', '19:00');

        $this->listMeetupsPage()
            ->upcomingMeetup('Coding Dojo')
            ->readMore($this->browser)
            ->assertScheduledFor('April 27, 2026 19:00');
    }
}
