<?php
declare(strict_types=1);

namespace AppTest;

use MeetupOrganizing\Application\SignUp;

final class Invoicing2Test extends AbstractApplicationTest
{
    public function testCreateInvoice(): void
    {
        $organizerId = $this->application->signUp(
            new SignUp(
                'Organizer',
                'organizer@gmail.com',
                'Organizer'
            )
        );

        // Given an organizer has scheduled two meetups in January 2022
        // @TODO check Year and Month and OrganizerId
        $this->meetups->setScheduledMeetupsCount(2);

        // When we create an invoice for this organizer
        $this->application->createInvoice($organizerId, 2022, 1);

        $invoices = $this->application->listInvoices($organizerId);

        // Then the amount of the invoice should be 10.00
        self::assertCount(1, $invoices);
        $invoice = $invoices[0];
        self::assertEquals('10.00', $invoice->amount());
        self::assertEquals('1/2022', $invoice->period());
    }

    public function testNoInvoiceWillBeCreated(): void
    {
        $organizerId = $this->application->signUp(
            new SignUp(
                'Organizer',
                'organizer@gmail.com',
                'Organizer'
            )
        );

        // Given no meetups were scheduled
        $this->meetups->setScheduledMeetupsCount(0);

        $this->application->createInvoice($organizerId, 2022, 1);

        $invoices = $this->application->listInvoices($organizerId);

        self::assertCount(0, $invoices);
    }
}
