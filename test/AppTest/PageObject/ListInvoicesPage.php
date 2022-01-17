<?php

declare(strict_types=1);

namespace AppTest\PageObject;

use Symfony\Component\DomCrawler\Crawler;

final class ListInvoicesPage extends AbstractPageObject
{
    public function invoiceAmountForPeriod(string $period): string
    {
        foreach ($this->crawler->filter('.invoice') as $invoiceNode) {
            $invoiceCrawler = new Crawler($invoiceNode, $this->crawler->getUri());

            if (trim($invoiceCrawler->filter('.period')->text()) === $period) {
                return trim($invoiceCrawler->filter('.amount')->text());
            }
        }

        throw new \RuntimeException('Could not find an invoice for period ' . $period);
    }
}
