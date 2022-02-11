<?php
declare(strict_types=1);

namespace AppTest;

use App\ScheduleMeetup;
use Billing\ViewModel\Invoice;
use MeetupOrganizing\Application\SignUp;

final class InvoicingApplicationTest extends AbstractApplicationTest
{
    public function testCreateInvoice(): void
    {
        $organizerId = $this->application->signUp(
            new SignUp('Organizer', 'organizer@gmail.com', 'Organizer')
        );
        $administratorId = $this->application->signUp(
            new SignUp('Administrator', 'administrator@gmail.com', 'Administrator')
        );

        $this->application->scheduleMeetup(
            new ScheduleMeetup(
                $organizerId,
                'Meetup 1',
                'Description',
                '2022-01-10 20:00',
            )
        );
        $this->application->scheduleMeetup(
            new ScheduleMeetup(
                $organizerId,
                'Meetup 2',
                'Description',
                '2022-01-17 20:00',
            )
        );

        $this->application->createInvoice(
            2022,
            1,
            $organizerId,
        );

        // find the invoice for this organizer and period
        $invoices = $this->application->listInvoices($organizerId);
        $invoicesForPeriod = array_filter(
            $invoices,
            fn (Invoice $invoice) => $invoice->period() === '1/2022'
        );
        self::assertCount(1, $invoicesForPeriod);

        $invoice = $invoicesForPeriod[0];
        self::assertEquals('10.00', $invoice->amount());
    }
}
