<?php

declare(strict_types=1);

namespace AppTest\PageObject;

use Symfony\Component\BrowserKit\HttpBrowser;

final class ScheduleMeetupPage extends AbstractPageObject
{
    public function scheduleMeetup(
        HttpBrowser $browser,
        string $name,
        string $description,
        string $date,
        string $time
    ): void {
        $browser->submitForm('Schedule this meetup', [
            'name' => $name,
            'description' => $description,
            'scheduleForDate' => $date,
            'scheduleForTime' => $time,
        ]);

        self::assertSuccessfulResponse($browser);
    }
}
