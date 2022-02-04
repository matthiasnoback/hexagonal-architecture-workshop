<?php
declare(strict_types=1);

namespace AppTest;

use MeetupOrganizing\Application\SignUp;

final class Invoicing2Test extends AbstractApplicationTest
{
    public function testCreateInvoice(): void
    {
        $organizerId = $this->application->signUp(
            new SignUp(
                'Organizer',
                'organizer@gmail.com',
                'Organizer'
            )
        );

        $this->application->scheduleMeetup(
            $organizerId,
            'Meetup 1',
            'Description',
            '2022-01-10 20:00'
        );
        $this->application->scheduleMeetup(
            $organizerId,
            'Meetup 2',
            'Description',
            '2022-01-17 20:00'
        );

        $this->application->createInvoice($organizerId, 2022, 1);

        $invoices = $this->application->listInvoices($organizerId);

        self::assertCount(1, $invoices);
        $invoice = $invoices[0];
        self::assertEquals('10.00', $invoice->amount());
        self::assertEquals('1/2022', $invoice->period());
    }
}
