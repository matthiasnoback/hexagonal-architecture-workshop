<?php

declare(strict_types=1);

namespace AppTest;

use MeetupOrganizing\Application\SignUp;

final class ApplicationLevelInvoicingTest extends AbstractApplicationTest
{
    public function testCreateInvoice(): void
    {
        $organizerId = $this->application->signUp(new SignUp('Organizer', 'organizer@gmail.com', 'Organizer'));

        $this->application->scheduleMeetup($organizerId, 'Meetup 1', 'Description', '2023-01-10 20:00');
        $this->application->scheduleMeetup($organizerId, 'Meetup 2', 'Description', '2023-01-17 20:00');

        $invoiceId = $this->application->createInvoice($organizerId, 2023, 1);

        self::assertIsInt($invoiceId);

        $invoicesForOrganizer = $this->application->listInvoices($organizerId);
        self::assertCount(1, $invoicesForOrganizer);

        self::assertEquals($organizerId, $invoicesForOrganizer[0]->organizerId());
        self::assertEquals('10.00', $invoicesForOrganizer[0]->amount());
    }
}
