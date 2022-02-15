<?php
declare(strict_types=1);

namespace AppTest;

use AppTest\Integration\BillableMeetupsForTest;
use Billing\BillableMeetups;
use Billing\ViewModel\Invoice;
use MeetupOrganizing\Application\SignUp;

final class InvoicingIntegrationTest extends AbstractApplicationTest
{
    public function testCreatesAnInvoiceForGivenOrganizerAndMonth(): void
    {
        $organizerId = $this->application->signUp(
            new SignUp('Organizer', 'organizer@gmail.com', 'Organizer'),
        );

        // Given 2 meetups were scheduled in January 2022 by this organizer
        $billableMeetups = $this->container->get(BillableMeetups::class);
        self::assertInstanceOf(BillableMeetupsForTest::class, $billableMeetups);
        $year = 2022;
        $month = 1;
        $billableMeetups->setCount(
            $organizerId,
            $year,
            $month,
            2,
        );

        // When we create an invoice for this month
        $this->application->createInvoice(
            $organizerId,
            $year,
            $month,
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
