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

        // A meetup in January 2022
        $this->application->scheduleMeetup(
            new ScheduleMeetup(
                $organizerId,
                'Meetup 1',
                'Description',
                '2022-01-10',
                '20:00',
            )
        );
        // Another meetup in January 2022
        $this->application->scheduleMeetup(
            new ScheduleMeetup(
                $organizerId,
                'Meetup 2',
                'Description',
                '2022-01-17',
                '20:00',
            )
        );
        // A meetup in December 2021
        $this->application->scheduleMeetup(
            new ScheduleMeetup(
                $organizerId,
                'Meetup 3',
                'Description',
                '2021-12-05',
                '18:00',
            )
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
