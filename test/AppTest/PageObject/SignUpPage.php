<?php

declare(strict_types=1);

namespace AppTest\PageObject;

use Symfony\Component\BrowserKit\HttpBrowser;

final class SignUpPage extends AbstractPageObject
{
    public function signUp(HttpBrowser $browser, string $name, string $emailAddress, string $userType): void
    {
        $browser->submitForm('Sign up', [
            'name' => $name,
            'emailAddress' => $emailAddress,
            'userType' => $userType,
        ]);

        self::assertSuccessfulResponse($browser);
    }
}
