<?php

declare(strict_types=1);

namespace AppTest;

use MeetupOrganizing\Application\SignUp;

final class ApplicationLevelInvoicingTest extends AbstractApplicationTest
{
    public function testCreateInvoice(): void
    {
        $this->nowIs('2023-01-01');
        $organizerId = $this->application->signUp(new SignUp('Organizer', 'organizer@gmail.com', 'Organizer'));

        $this->application->scheduleMeetup($organizerId, 'Meetup 1', 'Description', '2023-01-10', '20:00');
        $this->application->scheduleMeetup($organizerId, 'Meetup 2', 'Description', '2023-01-17', '20:00');

        $this->nowIs('2023-02-01');
        self::assertTrue($this->application->createInvoice($organizerId, 2023, 1));

        $invoices = $this->application->listInvoices($organizerId);
        self::assertCount(1, $invoices);
        self::assertSame('1/2023', $invoices[0]->period());
        self::assertSame('10.00', $invoices[0]->amount());
    }
}
