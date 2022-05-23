<?php

declare(strict_types=1);

namespace AppTest\PageObject;

use DOMNode;
use PHPUnit\Framework\Assert;
use Symfony\Component\BrowserKit\HttpBrowser;

final class MeetupDetailsPage extends AbstractPageObject
{
    public function cancelMeetup(HttpBrowser $browser): void
    {
        $browser->submitForm('Cancel this meetup');

        self::assertSuccessfulResponse($browser);
    }

    public function rsvpToMeetup(HttpBrowser $browser): void
    {
        $browser->submitForm('RSVP');

        self::assertSuccessfulResponse($browser);
    }

    /**
     * @return array<string>
     */
    public function attendees(): array
    {
        return array_map(
            fn (DOMNode $node) => trim($node->textContent),
            iterator_to_array($this->crawler->filter('.attendees li .name'))
        );
    }

    public function rescheduleMeetup(HttpBrowser $browser): RescheduleMeetupPage
    {
        $page = new RescheduleMeetupPage($browser->clickLink('Reschedule this meetup'));

        self::assertSuccessfulResponse($browser);

        return $page;
    }

    public function assertScheduledFor(string $expected): void
    {
        Assert::assertEquals($expected, trim($this->crawler->filter('.meetup-scheduled-for')->text()));
    }

    public function cancelRsvp(HttpBrowser $browser): void
    {
        $browser->submitForm('Cancel RSVP');

        self::assertSuccessfulResponse($browser);
    }
}
