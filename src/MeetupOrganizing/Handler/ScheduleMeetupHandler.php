<?php

declare(strict_types=1);

namespace MeetupOrganizing\Handler;

use App\ApplicationInterface;
use App\Mapping;
use App\ScheduleMeetup;
use App\Session;
use Assert\Assert;
use Exception;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use MeetupOrganizing\Entity\ScheduledDate;
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
        private readonly ApplicationInterface $application,
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

            $user = $this->session->getLoggedInUser();
            Assert::that($user)->notNull();

            $command = new ScheduleMeetup(
                $user->userId()->asString(),
                Mapping::getString($formData, 'name'),
                Mapping::getString($formData, 'description'),
                Mapping::getString($formData, 'scheduleForDate') . ' ' . Mapping::getString($formData, 'scheduleForTime')
            );

            if ($command->name() === '') {
                $formErrors['name'][] = 'Provide a name';
            }
            if ($command->description() === '') {
                $formErrors['description'][] = 'Provide a description';
            }
            try {
                ScheduledDate::fromString($command->dateAndTime());
            } catch (Exception) {
                $formErrors['scheduleFor'][] = 'Invalid date/time';
            }

            if ($formErrors === []) {
                $meetupId = $this->application->scheduleMeetup($command);

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
