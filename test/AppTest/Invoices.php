<?php

namespace AppTest;

use Billing\ViewModel\Invoice;

final class Invoices
{
    /**
     * @param list<Invoice> $invoices
     */
    public function __construct(private readonly array $invoices)
    {
    }

    public function invoiceFor(string $period): Invoice
    {
        foreach ($this->invoices as $invoice) {
            if ($invoice->period() === $period) {
                return $invoice;
            }
        }

        throw new \RuntimeException('No invoice found for period ' . $period);
    }
}
