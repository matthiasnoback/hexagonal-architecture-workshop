<?php

declare(strict_types=1);

namespace AppTest\PageObject;

use App\Entity\UserRepository;
use Symfony\Component\BrowserKit\HttpBrowser;

final class LoginPage extends AbstractPageObject
{
    public function logIn(HttpBrowser $browser, string $emailAddress): void
    {
        $browser->submitForm('Log in', [
            'emailAddress' => $emailAddress,
        ]);
    }

    public function logInAsRegularUser(HttpBrowser $browser): void
    {
        $this->logInAs($browser, UserRepository::REGULAR_USER_ID);
    }

    private function logInAs(HttpBrowser $browser, int $userId): void
    {
        $form = $this->crawler->selectButton('Switch')
            ->form();
        $form['userId']->setValue((string) $userId);

        $browser->submit($form);
    }
}
