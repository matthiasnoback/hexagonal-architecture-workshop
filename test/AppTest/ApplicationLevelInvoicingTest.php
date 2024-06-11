<?php

declare(strict_types=1);

namespace AppTest;

use Billing\FakeMeetupRepository;
use Billing\MeetupRepository;
use MeetupOrganizing\Application\SignUp;

final class ApplicationLevelInvoicingTest extends AbstractApplicationTest
{
    public function testCreateInvoice(): void
    {
        $organizerId = $this->application->signUp(new SignUp('Organizer', 'organizer@gmail.com', 'Organizer'));

        // This organizer has scheduled 2 meetups in January 2023
        /** @var FakeMeetupRepository $meetupRepository */
        $meetupRepository = $this->container->get(MeetupRepository::class);
        $meetupRepository->setCount($organizerId, 2);

        $invoiceId = $this->application->createInvoice($organizerId, 2023, 1);

        self::assertIsInt($invoiceId);

        $invoicesForOrganizer = $this->application->listInvoices($organizerId);
        self::assertCount(1, $invoicesForOrganizer);

        self::assertEquals($organizerId, $invoicesForOrganizer[0]->organizerId());
        self::assertEquals('10.00', $invoicesForOrganizer[0]->amount());
    }
}
