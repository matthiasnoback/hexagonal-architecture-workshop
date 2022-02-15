<?php
declare(strict_types=1);

namespace AppTest;

use App\ScheduleMeetup;
use Billing\ViewModel\Invoice;
use MeetupOrganizing\Application\SignUp;

final class InvoicingIntegrationTest extends AbstractApplicationTest
{
    public function testCreatesAnInvoiceForGivenOrganizerAndMonth(): void
    {
        $organizerId = $this->application->signUp(
            new SignUp('Organizer', 'organizer@gmail.com', 'Organizer'),
        );

        $this->application->createInvoice(
            $organizerId,
            2022,
            1,
        );

        // list invoices
        $allInvoicesForOrganizer = $this->application->listInvoices($organizerId);

        $invoicesForMonth = array_values(array_filter(
            $allInvoicesForOrganizer,
            fn (Invoice $invoice) => $invoice->period() === '1/2022'
        ));
        self::assertCount(1, $invoicesForMonth);
        self::assertSame('10.00', $invoicesForMonth[0]->amount());
    }
}
