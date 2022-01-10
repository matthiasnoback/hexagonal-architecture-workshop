<?php

declare(strict_types=1);

namespace AppTest\PageObject;

use RuntimeException;

final class ListMeetupsPage extends AbstractPageObject
{
    /**
     * @return array<MeetupSnippet>
     */
    public function upcomingMeetups(): array
    {
        return MeetupSnippet::createManyFromCrawler($this->crawler->filter('.upcoming-meetups .meetup'));
    }

    public function upcomingMeetup(string $name): MeetupSnippet
    {
        foreach ($this->upcomingMeetups() as $upcomingMeetup) {
            if ($upcomingMeetup->name() === $name) {
                return $upcomingMeetup;
            }
        }

        throw new RuntimeException('Could not find upcoming meetup ' . $name);
    }
}
