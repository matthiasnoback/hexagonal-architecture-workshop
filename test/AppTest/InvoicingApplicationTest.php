<?php
declare(strict_types=1);

namespace AppTest;

use Billing\ViewModel\Invoice;
use MeetupOrganizing\Application\SignUp;

final class InvoicingApplicationTest extends AbstractApplicationTest
{
    public function testCreateInvoice(): void
    {
        $organizerId = $this->application->signUp(
            new SignUp('Organizer', 'organizer@gmail.com', 'Organizer')
        );

        $this->application->scheduleMeetup('Meetup 1', 'Description', '2022-01-10 20:00', $organizerId);

        $this->application->createInvoice(
            2022,
            1,
            $organizerId
        );

        $invoices = $this->application->listInvoices($organizerId);
        self::assertCount(1, $invoices);

        $invoice = reset($invoices);
        self::assertInstanceOf(Invoice::class, $invoice);
        self::assertSame('5.00', $invoice->amount());
    }

    public function test_it_does_not_invoice_when_no_meetups_when_no_meetups_were_scheduled(): void
    {
        $organizerId = $this->application->signUp(
            new SignUp('Organizer', 'organizer@gmail.com', 'Organizer')
        );

        // Given no meetups were scheduled by a given organizer in January 2022

        // When we invoice this organizer for January 2022
        $this->application->createInvoice(
            2022,
            1,
            $organizerId
        );

        // Then they should not be invoiced
        $invoices = $this->application->listInvoices($organizerId);
        self::assertCount(0, $invoices);
    }
}
