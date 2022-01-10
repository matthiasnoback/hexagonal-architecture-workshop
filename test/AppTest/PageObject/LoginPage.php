<?php

declare(strict_types=1);

namespace AppTest\PageObject;

use App\Entity\UserRepository;
use Symfony\Component\BrowserKit\HttpBrowser;

final class LoginPage extends AbstractPageObject
{
    public function logInAsRegularUser(HttpBrowser $browser): void
    {
        $this->logInAs($browser, UserRepository::REGULAR_USER_ID);
    }

    public function logInAsOrganizer(HttpBrowser $browser): void
    {
        $this->logInAs($browser, UserRepository::ORGANIZER_ID);
    }

    private function logInAs(HttpBrowser $browser, int $userId): void
    {
        $form = $this->crawler->selectButton('Switch')
            ->form();
        $form['userId']->setValue((string) $userId);

        $browser->submit($form);
    }
}
