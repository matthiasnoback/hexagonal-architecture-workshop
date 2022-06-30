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

        $this->setServerTime('2022-07-01');

        $this->login('organizer@gmail.com');
        $this->scheduleMeetup('Meetup 1', 'Description', '2022-08-10', '20:00');
        $this->scheduleMeetup('Meetup 2', 'Description', '2022-08-17', '20:00');
        $this->logout();

        $this->login('administrator@gmail.com');

        $this->listOrganizersPage()
            ->firstOrganizer()
            ->goToCreateInvoicePage($this->browser)
            ->createInvoice($this->browser, '2022', '8');
        $this->flashMessagesShouldContain('Invoice created');

        $invoicesPage = $this->listOrganizersPage()
            ->firstOrganizer()
            ->listInvoices($this->browser);
        self::assertEquals('10.00', $invoicesPage->invoiceAmountForPeriod('8/2022'));
    }

    private function listOrganizersPage(): ListOrganizersPage
    {
        return new ListOrganizersPage($this->browser->request('GET', '/admin/list-organizers'));
    }
}
