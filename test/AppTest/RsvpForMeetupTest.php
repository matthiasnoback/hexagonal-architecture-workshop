<?php

declare(strict_types=1);

namespace AppTest;

final class RsvpForMeetupTest extends AbstractBrowserTest
{
    public function testRsvp(): void
    {
        $this->signUp('Organizer', 'organizer@gmail.com', 'Organizer');
        $this->login('organizer@gmail.com');

        $this->scheduleMeetup('Coding Dojo', 'Some description', '2024-10-10', '20:00');

        $this->logout();

        $this->signUp('Regular user', 'user@gmail.com', 'RegularUser');
        $this->login('user@gmail.com');

        $this->rsvpForMeetup('Coding Dojo');

        $this->flashMessagesShouldContain('You have successfully RSVP-ed to this meetup');

        $this->listOfAttendeesShouldContain('Coding Dojo', 'Regular user');
    }

    private function rsvpForMeetup(string $name): void
    {
        $this->listMeetupsPage()
            ->upcomingMeetup($name)
            ->readMore($this->browser)
            ->rsvpToMeetup($this->browser);
    }

    private function listOfAttendeesShouldContain(string $meetupName, string $attendeeName): void
    {
        self::assertContains(
            $attendeeName,
            $this->listMeetupsPage()
                ->upcomingMeetup($meetupName)
                ->readMore($this->browser)
                ->attendees()
        );
    }
}
