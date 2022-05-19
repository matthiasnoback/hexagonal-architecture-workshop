<?php
declare(strict_types=1);

namespace AppTest\PageObject;

use Symfony\Component\BrowserKit\HttpBrowser;

final class RescheduleMeetupPage extends AbstractPageObject
{
    public function reschedule(HttpBrowser $browser, string $date, string $time): void
    {
        $browser->submitForm('Reschedule', [
            'scheduleForDate' => $date,
            'scheduleForTime' => $time,
        ]);

        self::assertSuccessfulResponse($browser);
    }
}
