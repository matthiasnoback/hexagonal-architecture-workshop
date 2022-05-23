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

    public function testCancelRsvp(): void
    {
        $this->signUp('Organizer', 'organizer@gmail.com', 'Organizer');
        $this->login('organizer@gmail.com');

        $this->scheduleMeetup('Coding Dojo', 'Some description', '2024-10-10', '20:00');

        $this->logout();

        $this->signUp('Regular user', 'user@gmail.com', 'RegularUser');
        $this->login('user@gmail.com');

        $this->rsvpForMeetup('Coding Dojo');

        $this->listOfAttendeesShouldContain('Coding Dojo', 'Regular user');

        $this->cancelRsvp('Coding Dojo');

        $this->listOfAttendeesShouldNotContain('Coding Dojo', 'Regular user');
    }
}
