<?php
declare(strict_types=1);

namespace AppTest;

use App\CreateInvoice;
use Billing\MeetupRepository;
use Billing\MeetupRepositoryForTesting;
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

        // Given 2 meetups were scheduled by an organizer in January 2022
        $meetupRepository = $this->container->get(MeetupRepository::class);
        assert($meetupRepository instanceof MeetupRepositoryForTesting);
        $meetupRepository->mockNumberOfMeetups($organizerId, 2022, 1, 2);

        // When we create an invoice for this organizer, for January 2022
        $this->application->createInvoice(
            new CreateInvoice($organizerId, 2022, 1)
        );

        // Then this invoice should have an amount 10.00
        $invoices = $this->application->listInvoices($organizerId);
        self::assertCount(1, $invoices);
        $invoice = $invoices[0];

        self::assertSame('10.00', $invoice->amount());
        self::assertSame('1/2022', $invoice->period());
    }
}
