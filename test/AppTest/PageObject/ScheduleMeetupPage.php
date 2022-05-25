<?php

declare(strict_types=1);

namespace AppTest\PageObject;

use LogicException;
use PHPUnit\Framework\Assert;
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

    public function scheduleMeetupUnsuccessfully(
        HttpBrowser $browser,
        string $name,
        string $description,
        string $date,
        string $time
    ): self {
        $crawler = $browser->submitForm('Schedule this meetup', [
            'name' => $name,
            'description' => $description,
            'scheduleForDate' => $date,
            'scheduleForTime' => $time,
        ]);

        self::assertUnsuccessfulResponse($browser);

        return new self($crawler);
    }

    public function assertFormErrorsContains(string $expectedError): void
    {
        $feedback = $this->crawler->filter('.form-error');
        if (count($feedback) === 0) {
            throw new LogicException('No form errors found');
        }

        Assert::assertStringContainsString(
            $expectedError,
            $feedback->text()
        );
    }
}
