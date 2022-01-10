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
        $form = $this->crawler->selectButton('Schedule this meetup')
            ->form();

        $form['name']->setValue($name);
        $form['description']->setValue($description);
        $form['scheduleForDate']->setValue($date);
        $form['scheduleForTime']->setValue($time);

        $browser->submit($form);
    }
}
