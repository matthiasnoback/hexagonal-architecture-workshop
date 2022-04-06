<?php

declare(strict_types=1);

namespace AppTest\PageObject;

final class ListOrganizersPage extends AbstractPageObject
{
    public function firstOrganizer(): OrganizerSnippet
    {
        $organizers = $this->crawler->filter('.organizer');

        if (count($organizers) === 0) {
            throw new \RuntimeException('No organizers found');
        }

        return new OrganizerSnippet($organizers->first());
    }
}
