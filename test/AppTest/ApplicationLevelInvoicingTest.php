<?php

declare(strict_types=1);

namespace AppTest;

use MeetupOrganizing\Application\ScheduleMeetup;
use MeetupOrganizing\Application\SignUp;

final class ApplicationLevelInvoicingTest extends AbstractApplicationTest
{
    public function testCreateInvoice(): void
    {
        $organizerId = $this->application->signUp(new SignUp('Organizer', 'organizer@gmail.com', 'Organizer'));

        $this->application->scheduleMeetup(
            new ScheduleMeetup($organizerId, 'Meetup 1', 'Description', '2023-01-10 20:00')
        );
        $this->application->scheduleMeetup(
            new ScheduleMeetup($organizerId, 'Meetup 2', 'Description', '2023-01-17 20:00')
        );

        $this->application->createInvoice(
            $organizerId,
            1,
            2023
        );

        $invoices = new Invoices($this->application->listInvoices($organizerId));

        $this->assertEquals('10.00', $invoices->invoiceFor('1/2023')->amount());
    }
}
