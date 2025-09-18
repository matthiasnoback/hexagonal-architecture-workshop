<?php

declare(strict_types=1);

namespace AppTest;

use MeetupOrganizing\Application\SignUp;

final class ApplicationLevelInvoicingTest extends AbstractApplicationTest
{
    public function testCreateInvoice(): void
    {
        $organizerId = $this->application->signUp(new SignUp('Organizer', 'organizer@gmail.com', 'Organizer'));

        $this->application->scheduleMeetup($organizerId, 'Meetup 1', 'Description', '2025-01-10', '20:00');
        $this->application->scheduleMeetup($organizerId, 'Meetup 2', 'Description', '2025-01-17', '20:00');

        self::assertTrue($this->application->createInvoice($organizerId, 2025, 1));

        $invoices = $this->application->listInvoices($organizerId);
        self::assertCount(1, $invoices);
        self::assertSame('1/2025', $invoices[0]->period());
        self::assertSame('10.00', $invoices[0]->amount());
    }
}
