<?php
declare(strict_types=1);

namespace AppTest;

use MeetupOrganizing\Application\SignUp;

final class ApplicationLevelInvoicingTest extends AbstractApplicationTest
{
    public function testCreateInvoice(): void
    {
        $organizerId = $this->application->signUp(
            new SignUp('Organizer', 'organizer@gmail.com', 'Organizer')
        );

        // @TODO remove useless assertion
        self::assertIsString($organizerId);

        // @TODO let the organizer schedule a meetup (see InvoicingTest for sample data)
        // @TODO let the organizer schedule another meetup (see InvoicingTest for sample data)
        // @TODO create an invoice for the organizer for January 2022
        // @TODO list the invoices for the organizer
        // @TODO assert that the only invoice is an invoice for January 2022 with an amount of 10.00
    }
}
