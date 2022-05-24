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
use DateTimeImmutable;
use DateTimeInterface;
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
        $this->setServerTime(new DateTimeImmutable());
    }

    protected function tearDown(): void
    {
        $this->logout();
    }

    protected function scheduleMeetup(string $name, string $description, string $date, string $time): void
    {
        (new ScheduleMeetupPage($this->browser->request('GET', '/schedule-meetup')))
            ->scheduleMeetup($this->browser, $name, $description, $date, $time);
    }

    protected function scheduleMeetupProducesFormError(string $name, string $description, string $date, string $time, string $expectedError): void
    {
        (new ScheduleMeetupPage($this->browser->request('GET', '/schedule-meetup')))
            ->scheduleMeetupUnsuccessfully($this->browser, $name, $description, $date, $time)
            ->assertFormErrorsContains($expectedError);
    }

    protected function cancelMeetup(string $name): void
    {
        $this->meetupDetails($name)
            ->cancelMeetup($this->browser);
    }

    protected function rescheduleMeetup(string $name, string $date, string $time): void
    {
        $this->meetupDetails($name)
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

    protected function rsvpForMeetup(string $name): void
    {
        $this->meetupDetails($name)
            ->rsvpToMeetup($this->browser);
    }

    protected function cancelRsvp(string $name): void
    {
        $this->meetupDetails($name)
            ->cancelRsvp($this->browser);
    }

    protected function listOfAttendeesShouldContain(string $meetupName, string $attendeeName): void
    {
        self::assertContains($attendeeName, $this->meetupDetails($meetupName) ->attendees());
    }

    protected function listOfAttendeesShouldNotContain(string $meetupName, string $attendeeName): void
    {
        self::assertNotContains($attendeeName, $this->meetupDetails($meetupName) ->attendees());
    }

    private function meetupDetails(string $meetupName): PageObject\MeetupDetailsPage
    {
        return $this->listMeetupsPage()
            ->upcomingMeetup($meetupName)
            ->readMore($this->browser);
    }

    protected function setServerTime(DateTimeImmutable $dateTime): void
    {
        self::assertInstanceOf(HttpBrowser::class, self::$httpBrowserClient);

        self::$httpBrowserClient->setServerParameter(
            'HTTP_X-CURRENT-TIME',
            $dateTime->format(DateTimeInterface::ATOM)
        );
    }
}
