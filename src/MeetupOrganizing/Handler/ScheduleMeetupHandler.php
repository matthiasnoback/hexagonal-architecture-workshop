<?php

declare(strict_types=1);

namespace MeetupOrganizing\Handler;

use App\EventDispatcher;
use App\Session;
use Assert\Assert;
use Doctrine\DBAL\Connection;
use Exception;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use MeetupOrganizing\Entity\ScheduledDate;
use MeetupOrganizing\MeetupWasScheduled;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ScheduleMeetupHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly Session $session,
        private readonly TemplateRendererInterface $renderer,
        private readonly RouterInterface $router,
        private readonly Connection $connection,
        private readonly EventDispatcher $eventDispatcher,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $formErrors = [];
        $formData = [
            'name' => '',
            'description' => '',
            'scheduleForDate' => '',
            // This is a nice place to set some defaults
            'scheduleForTime' => '20:00',
        ];

        if ($request->getMethod() === 'POST') {
            $formData = $request->getParsedBody();
            Assert::that($formData)->isArray();

            if ($formData['name'] === '') {
                $formErrors['name'][] = 'Provide a name';
            }
            if ($formData['description'] === '') {
                $formErrors['description'][] = 'Provide a description';
            }
            try {
                ScheduledDate::fromString($formData['scheduleForDate'] . ' ' . $formData['scheduleForTime']);
            } catch (Exception) {
                $formErrors['scheduleFor'][] = 'Invalid date/time';
            }

            if ($formErrors === []) {
                $user = $this->session->getLoggedInUser();
                Assert::that($user)->notNull();

                $record = [
                    'organizerId' => $user
                        ->userId()
                        ->asString(),
                    'name' => $formData['name'],
                    'description' => $formData['description'],
                    'scheduledFor' => $formData['scheduleForDate'] . ' ' . $formData['scheduleForTime'],
                    'wasCancelled' => 0,
                ];
                $this->connection->insert('meetups', $record);

                $meetupId = (int) $this->connection->lastInsertId();

                $this->eventDispatcher->dispatch(
                    new MeetupWasScheduled(
                        (string) $meetupId,
                        $user->userId()
                    )
                );

                $this->session->addSuccessFlash('Your meetup was scheduled successfully');

                return new RedirectResponse(
                    $this->router->generateUri('meetup_details', [
                        'id' => $meetupId,
                    ])
                );
            }
        }

        return new HtmlResponse(
            $this->renderer->render(
                'app::schedule-meetup.html.twig',
                [
                    'formData' => $formData,
                    'formErrors' => $formErrors,
                ]
            )
        );
    }
}
