<?php

declare(strict_types=1);

namespace AppTest\PageObject;

use Symfony\Component\BrowserKit\HttpBrowser;

final class OrganizerSnippet extends AbstractPageObject
{
    public function createInvoice(HttpBrowser $browser): CreateInvoicePage
    {
        return new CreateInvoicePage($browser->click($this->crawler->filter('.create-invoice')->link()));
    }

    public function listInvoices(HttpBrowser $browser): ListInvoicesPage
    {
        return new ListInvoicesPage($browser->click($this->crawler->filter('.list-invoices')->link()));
    }
}
