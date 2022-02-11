<?php
declare(strict_types=1);

namespace AppTest;

use Billing\MeetupCounts;
use Billing\ViewModel\Invoice;
use MeetupOrganizing\Application\SignUp;

final class InvoicingApplicationTest extends AbstractApplicationTest
{
    private MeetupCountsForTesting $meetupCounts;

    protected function setUp(): void
    {
        parent::setUp();

        $meetupCounts = $this->container->get(MeetupCounts::class);
        self::assertInstanceOf(MeetupCountsForTesting::class, $meetupCounts);
        $this->meetupCounts = $meetupCounts;
    }

    public function testCreateInvoice(): void
    {
        $organizerId = $this->application->signUp(
            new SignUp('Organizer', 'organizer@gmail.com', 'Organizer')
        );

        $this->meetupCounts->setCount(2);

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
