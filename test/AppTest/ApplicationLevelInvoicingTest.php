<?php

declare(strict_types=1);

namespace AppTest;

use Billing\CountMeetups;
use Billing\InvoiceNotRequired;
use MeetupOrganizing\Application\SignUp;

/**
 * @group wip
 */
final class ApplicationLevelInvoicingTest extends AbstractApplicationTest
{
    public function testCreateInvoice(): void
    {
        $organizerId = $this->application->signUp(new SignUp('Organizer', 'organizer@gmail.com', 'Organizer'));

        // Given the organizer has scheduled 2 meetups in 2022/1
        /** @var CountMeetupsMock $countMeetups */
        $countMeetups = $this->container->get(CountMeetups::class);
        $countMeetups->countMethodShouldReturn(2022, 1, $organizerId, 2);

        $this->application->createInvoice(2022, 1, $organizerId);

        $invoices = $this->application->listInvoices($organizerId);

        $this->assertCount(1, $invoices);
        $invoice = $invoices[0];
        $this->assertSame('10.00', $invoice->amount());
        $this->assertSame('1/2022', $invoice->period());
    }

    public function testNoInvoiceRequired(): void
    {
        $organizerId = $this->application->signUp(new SignUp('Organizer', 'organizer@gmail.com', 'Organizer'));

        // Given the organizer has scheduled no meetups in 2022/1
        /** @var CountMeetupsMock $countMeetups */
        $countMeetups = $this->container->get(CountMeetups::class);
        $countMeetups->countMethodShouldReturn(2022, 1, $organizerId, 0);

        try {
            $this->application->createInvoice(2022, 1, $organizerId);
            $this->fail('Expected exception');
        } catch (InvoiceNotRequired) {
            $invoices = $this->application->listInvoices($organizerId);
            $this->assertCount(0, $invoices);
        }
    }
}
