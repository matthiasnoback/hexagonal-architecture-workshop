<?php

declare(strict_types=1);

namespace AppTest;

use MeetupOrganizing\Api\MeetupRepository;
use MeetupOrganizing\Application\SignUp;

final class ApplicationLevelInvoicingTest extends AbstractApplicationTest
{
    public function testCreateInvoice(): void
    {
        $organizerId = $this->application->signUp(new SignUp('Organizer', 'organizer@gmail.com', 'Organizer'));

        // This organizer has scheduled 2 meetups in January 2025
        $this->meetupRepository()->setMeetupCount($organizerId, 2025, 1, 2);
        $this->application->createInvoice($organizerId, 2025, 1);

        $invoices = $this->application->listInvoices($organizerId);
        self::assertCount(1, $invoices);
        self::assertEquals($organizerId, $invoices[0]->organizerId());
        self::assertEquals('1/2025', $invoices[0]->period());
        self::assertEquals('10.00', $invoices[0]->amount());
    }

    private function meetupRepository(): MeetupRepositoryForTesting
    {
        $meetupRepository = $this->container->get(MeetupRepository::class);
        self::assertInstanceOf(MeetupRepositoryForTesting::class, $meetupRepository);

        return $meetupRepository;
    }
}
