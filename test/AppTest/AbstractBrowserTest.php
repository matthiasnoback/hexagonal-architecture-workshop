<?php

declare(strict_types=1);

namespace AppTest;

use App\SchemaManager;
use AppTest\PageObject\GenericPage;
use AppTest\PageObject\ListMeetupsPage;
use AppTest\PageObject\LoginPage;
use AppTest\PageObject\MeetupSnippet;
use AppTest\PageObject\ScheduleMeetupPage;
use AppTest\PageObject\SignUpPage;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\Panther\PantherTestCaseTrait;

abstract class AbstractBrowserTest extends TestCase
{
    use PantherTestCaseTrait;

    protected HttpBrowser $browser;

    protected function setUp(): void
    {
        $_ENV['APPLICATION_ENV'] = 'end_to_end_testing';

        /** @var ContainerInterface $container */
        $container = require 'config/container.php';

        /** @var SchemaManager $schemaManager */
        $schemaManager = $container->get(SchemaManager::class);
        $schemaManager->updateSchema();
        $schemaManager->truncateTables();

        self::$baseUri = 'http://web_testing:80';
        $this->browser = self::createHttpBrowserClient();
    }

    protected function scheduleMeetup(string $name, string $description, string $date, string $time): void
    {
        (new ScheduleMeetupPage($this->browser->request('GET', '/schedule-meetup')))
            ->scheduleMeetup($this->browser, $name, $description, $date, $time);
    }

    protected function cancelMeetup(string $name): void
    {
        $this->listMeetupsPage()
            ->upcomingMeetup($name)
            ->readMore($this->browser)
            ->cancelMeetup($this->browser);
    }

    protected function rescheduleMeetup(string $name, string $date, string $time): void
    {
        $this->listMeetupsPage()
            ->upcomingMeetup($name)
            ->readMore($this->browser)
            ->rescheduleMeetup($this->browser)
            ->reschedule($this->browser, $date, $time);
    }

    protected function assertUpcomingMeetupExists(string $expectedName): void
    {
        self::assertContains(
            $expectedName,
            array_map(fn (MeetupSnippet $meetup) => $meetup->name(), $this->listMeetupsPage() ->upcomingMeetups())
        );
    }

    protected function assertUpcomingMeetupDoesNotExist(string $expectedName): void
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

    protected function flashMessagesShouldContain(string $expectedMessage): void
    {
        self::assertContains($expectedMessage, (new GenericPage($this->browser->getCrawler()))->getFlashMessages());
    }

    protected function signUp(string $name, string $emailAddress, string $userType): void
    {
        (new SignUpPage($this->browser->request('GET', '/sign-up')))
            ->signUp($this->browser, $name, $emailAddress, $userType);
    }

    protected function login(string $emailAddress): void
    {
        (new LoginPage($this->browser->request('GET', '/login')))
            ->logIn($this->browser, $emailAddress);
    }

    protected function logout(): void
    {
        $this->browser->request('POST', '/logout');
    }

    protected function listOfAttendeesShouldContain(string $meetupName, string $attendeeName): void
    {
        self::assertContains(
            $attendeeName,
            $this->listMeetupsPage()
                ->upcomingMeetup($meetupName)
                ->readMore($this->browser)
                ->attendees()
        );
    }
}
