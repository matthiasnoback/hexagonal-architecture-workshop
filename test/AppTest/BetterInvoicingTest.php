<?php
declare(strict_types=1);

namespace AppTest;

use App\CreateInvoice;
use App\ScheduleMeetupCommand;
use MeetupOrganizing\Application\SignUp;

final class BetterInvoicingTest extends AbstractApplicationTest
{
    public function test(): void
    {
        $organizerId = $this->application->signUp(
            new SignUp(
                'Organizer',
                'organizer@gmail.com',
                'Organizer'
            )
        );

        $this->application->createInvoice(
            new CreateInvoice($organizerId, 2022, 1)
        );

        $invoices = $this->application->listInvoices($organizerId);
        self::assertCount(1, $invoices);
        $invoice = $invoices[0];

        self::assertSame('10.00', $invoice->amount());
        self::assertSame('1/2022', $invoice->period());
    }
}
