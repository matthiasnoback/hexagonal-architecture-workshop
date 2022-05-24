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

        // Enable this line for Part 2, Assignment 1:
        //$this->flashMessagesShouldContain('You have successfully RSVP-ed to this meetup');

        $this->assertUpcomingMeetupExists('Coding Dojo');

        // Enable this line for Part 2, Assignment 1:
        //$this->listOfAttendeesShouldContain('Coding Dojo', 'Organizer');
    }

    public function testNameShouldNotBeEmpty(): void
    {
        $this->signUp('Organizer', 'organizer@gmail.com', 'Organizer');
        $this->login('organizer@gmail.com');

        $this->scheduleMeetupProducesFormError('', 'Some description', '2024-10-10', '20:00', 'Provide a name');
    }

    public function testDescriptionShouldNotBeEmpty(): void
    {
        $this->signUp('Organizer', 'organizer@gmail.com', 'Organizer');
        $this->login('organizer@gmail.com');

        $this->scheduleMeetupProducesFormError('Some name', '', '2024-10-10', '20:00', 'Provide a description');
    }
}
