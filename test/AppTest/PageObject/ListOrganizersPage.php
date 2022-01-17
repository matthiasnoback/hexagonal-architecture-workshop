<?php

declare(strict_types=1);

namespace AppTest\PageObject;

final class ListOrganizersPage extends AbstractPageObject
{
    public function firstOrganizer(): OrganizerSnippet
    {
        return new OrganizerSnippet($this->crawler->filter('.organizer')->first());
    }
}
