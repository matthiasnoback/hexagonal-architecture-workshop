<?php

declare(strict_types=1);

namespace AppTest;

use AppTest\PageObject\ListOrganizersPage;

final class InvoicingTest extends AbstractBrowserTest
{
    public function testCreateInvoice(): void
    {
        $this->signUp('Organizer', 'organizer@gmail.com', 'Organizer');
        $this->signUp('Administrator', 'administrator@gmail.com', 'Administrator');

        $this->login('organizer@gmail.com');
        $this->scheduleMeetup('Meetup 1', 'Description', '2022-01-10', '20:00');
        $this->scheduleMeetup('Meetup 2', 'Description', '2022-01-17', '20:00');
        $this->logout();

        $this->login('administrator@gmail.com');

        $this->listOrganizersPage()
            ->firstOrganizer()
            ->createInvoice($this->browser)
            ->createInvoice($this->browser, '2022', '1');
        $this->flashMessagesShouldContain('Invoice created');

        $invoicesPage = $this->listOrganizersPage()
            ->firstOrganizer()
            ->listInvoices($this->browser);
        self::assertEquals('10.00', $invoicesPage->invoiceAmountForPeriod('1/2022'));
    }

    /**
     * Scenario: ("The one where ...") the organizer has scheduled two meetups but one has been cancelled
     *
     *   When cancelling a meetup, it should not be invoiced.
     */
    public function testOrganizerHasScheduledTwoMeetupsButOneHasBeenCancelled(): void
    {
        $this->signUp('Organizer', 'organizer@gmail.com', 'Organizer');
        $this->signUp('Administrator', 'administrator@gmail.com', 'Administrator');

        // Given an organizer who has scheduled 2 meetups in Dec 2022
        $this->login('organizer@gmail.com');
        $this->scheduleMeetup('Meetup 1', 'Description', '2022-12-10', '20:00');
        $this->scheduleMeetup('Meetup 2', 'Description', '2022-12-17', '20:00');
        // But they have cancelled one of them
        $this->cancelMeetup('Meetup 2');
        $this->logout();

        $this->login('administrator@gmail.com');

        // When we create an invoice for this organizer for Dec 2022
        $this->listOrganizersPage()
            ->firstOrganizer()
            ->createInvoice($this->browser)
            ->createInvoice($this->browser, '2022', '12');
        $this->flashMessagesShouldContain('Invoice created');

        // Then the invoice amount should be 5.00
        $invoicesPage = $this->listOrganizersPage()
            ->firstOrganizer()
            ->listInvoices($this->browser);
        self::assertEquals('5.00', $invoicesPage->invoiceAmountForPeriod('12/2022'));
    }

    private function listOrganizersPage(): ListOrganizersPage
    {
        return new ListOrganizersPage($this->browser->request('GET', '/admin/list-organizers'));
    }
}
