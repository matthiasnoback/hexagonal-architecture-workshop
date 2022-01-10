<?php

declare(strict_types=1);

namespace AppTest\PageObject;

use Symfony\Component\BrowserKit\HttpBrowser;

final class MeetupSnippet extends AbstractPageObject
{
    public function name(): string
    {
        return trim($this->crawler->filter('.name')->text());
    }

    public function readMore(HttpBrowser $browser): MeetupDetailsPage
    {
        return new MeetupDetailsPage($browser->click($this->crawler->filter('a.read-more')->link()));
    }
}
