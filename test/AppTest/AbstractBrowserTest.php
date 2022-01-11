<?php

declare(strict_types=1);

namespace AppTest;

use AppTest\PageObject\GenericPage;
use AppTest\PageObject\ListMeetupsPage;
use AppTest\PageObject\LoginPage;
use AppTest\PageObject\MeetupSnippet;
use AppTest\PageObject\ScheduleMeetupPage;
use AppTest\PageObject\SignUpPage;
use PHPUnit\Framework\TestCase;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Panther\PantherTestCaseTrait;

abstract class AbstractBrowserTest extends TestCase
{
    use PantherTestCaseTrait;

    protected HttpBrowser $browser;

    protected function setUp(): void
    {
        $filesystem = new Filesystem();
        $filesystem->remove(__DIR__ . '/../../var/app-testing.sqlite');

        $this->browser = self::createHttpBrowserClient([
            'env' => [
                'APPLICATION_ENV' => 'testing',
            ],
        ]);
    }

    protected function iAmLoggedInAsOrganizer(): void
    {
        $this->logout();

        $this->signUpPage()
            ->signUp($this->browser, 'Organizer', 'organizer@gmail.com', 'Organizer');

        $this->loginPage()
            ->logIn($this->browser, 'organizer@gmail.com');
    }

    protected function iAmLoggedInAsRegularUser(): void
    {
        $this->logout();

        $this->signUpPage()
            ->signUp($this->browser, 'Regular user', 'user@gmail.com', 'RegularUser');

        $this->loginPage()
            ->logIn($this->browser, 'user@gmail.com');
    }

    protected function iScheduleAMeetup(string $name, string $description, string $date, string $time): void
    {
        (new ScheduleMeetupPage($this->browser->request('GET', '/schedule-meetup')))
            ->scheduleMeetup($this->browser, $name, $description, $date, $time);
    }

    protected function iCancelMeetup(string $name): void
    {
        $this->listMeetupsPage()
            ->upcomingMeetup($name)
            ->readMore($this->browser)
            ->cancelMeetup($this->browser);
    }

    protected function iShouldSeeUpcomingMeetup(string $expectedName): void
    {
        self::assertContains(
            $expectedName,
            array_map(fn (MeetupSnippet $meetup) => $meetup->name(), $this->listMeetupsPage() ->upcomingMeetups())
        );
    }

    protected function iShouldNotSeeUpcomingMeetup(string $expectedName): void
    {
        self::assertNotContains(
            $expectedName,
            array_map(fn (MeetupSnippet $meetup) => $meetup->name(), $this->listMeetupsPage() ->upcomingMeetups())
        );
    }

    protected function listMeetupsPage(): ListMeetupsPage
    {
        return new ListMeetupsPage($this->browser->request('GET', '/'));
    }

    protected function loginPage(): LoginPage
    {
        return new LoginPage($this->browser->request('GET', '/login'));
    }

    protected function flashMessagesShouldContain(string $expectedMessage): void
    {
        self::assertContains($expectedMessage, (new GenericPage($this->browser->getCrawler()))->getFlashMessages());
    }

    private function signUpPage(): SignUpPage
    {
        return new SignUpPage($this->browser->request('GET', '/sign-up'));
    }

    private function logout(): void
    {
        $this->browser->request('POST', '/logout');
    }
}
