<?php

declare(strict_types=1);

namespace AppTest\PageObject;

use AppTest\SuccessfulResponse;
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
            fn (\DOMNode $node) => trim($node->textContent),
            iterator_to_array($this->crawler->filter('.attendees li'))
        );
    }
}
