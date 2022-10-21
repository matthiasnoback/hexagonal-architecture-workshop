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
            new ScheduleMeetup(
                $organizerId,
                'Name 1',
                'Description 1',
                '2022-01-01',
                '20:00'
            )
        );
        $this->application->scheduleMeetup(
            new ScheduleMeetup(
                $organizerId,
                'Name 2',
                'Description 2',
                '2022-01-02',
                '21:00'
            )
        );

        $this->application->createInvoice(2022, 1, $organizerId);

        $invoices = $this->application->listInvoices($organizerId);

        $this->assertCount(1, $invoices);
        $invoice = $invoices[0];
        $this->assertSame('10.00', $invoice->amount());
        $this->assertSame('1/2022', $invoice->period());
    }
}
