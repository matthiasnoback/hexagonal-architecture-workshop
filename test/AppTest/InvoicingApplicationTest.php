<?php
declare(strict_types=1);

namespace AppTest;

use MeetupOrganizing\Application\SignUp;

final class InvoicingApplicationTest extends AbstractApplicationTest
{
    public function testCreateInvoice(): void
    {
        $organizerId = $this->application->signUp(new SignUp('Organizer', 'organizer@gmail.com', 'Organizer'));

        $this->application->scheduleMeetup($organizerId, 'Meetup 1', 'Description', '2022-08-10 20:00');
        $this->application->scheduleMeetup($organizerId, 'Meetup 2', 'Description', '2022-08-17 20:00');

        $this->application->createInvoice($organizerId, 2022, 8);

//        $invoices = $this->application->listInvoices();

//        self::assertEquals('10.00', $invoicesPage->invoiceAmountForPeriod('1/2022'));
    }
}
