<?php

namespace AppTest;

use Billing\ViewModel\Invoice;
use MeetupOrganizing\Application\SignUp;

class InvoicingHexagonalTest extends AbstractApplicationTest
{
    public function testCreateInvoice(): void
    {
        $organizerId = $this->application->signUp(
            new SignUp('Organizer', 'organizer@gmail.com', 'Organizer')
        );

        $this->application->scheduleMeeting($organizerId, 'Meetup 1', 'Description', '2022-01-10 20:00');
        $this->application->scheduleMeeting($organizerId, 'Meetup 2', 'Description', '2022-01-17 20:00');

        // create invoice for organizer and month
        $invoiceId = $this->application->createInvoice(2022, 1, $organizerId);
        self::assertNotNull($invoiceId);

        // assert created invoice has amount of 10 euros
        $invoice = array_filter($this->application->listInvoices($organizerId), fn (Invoice $invoice) => $invoice->invoiceId() === $invoiceId)[0];
        self::assertEquals('10.00', $invoice->amount());
    }
}
