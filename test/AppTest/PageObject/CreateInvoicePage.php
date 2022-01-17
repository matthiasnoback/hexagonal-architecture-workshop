<?php

declare(strict_types=1);

namespace AppTest\PageObject;

use Symfony\Component\BrowserKit\HttpBrowser;

final class CreateInvoicePage extends AbstractPageObject
{
    public function createInvoice(HttpBrowser $browser, string $year, string $month): void
    {
        $browser->submitForm('Create invoice', [
            'year' => $year,
            'month' => $month,
        ]);
    }
}
