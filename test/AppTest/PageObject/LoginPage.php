<?php

declare(strict_types=1);

namespace AppTest\PageObject;

use Symfony\Component\BrowserKit\HttpBrowser;

final class LoginPage extends AbstractPageObject
{
    public function logIn(HttpBrowser $browser, string $emailAddress): void
    {
        $browser->submitForm('Log in', [
            'emailAddress' => $emailAddress,
        ]);

        self::assertSuccessfulResponse($browser);
    }
}
