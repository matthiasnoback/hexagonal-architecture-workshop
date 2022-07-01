<?php

declare(strict_types=1);

namespace MeetupOrganizing\Handler;

use App\ApplicationInterface;
use App\Mapping;
use App\Session;
use Assert\Assert;
use Assert\Assertion;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ResponseFactory;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RescheduleMeetupHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly Connection $connection,
        private readonly Session $session,
        private readonly RouterInterface $router,
        private readonly ResponseFactory $responseFactory,
        private readonly TemplateRendererInterface $renderer,
        private readonly ApplicationInterface $application,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $loggedInUser = $this->session->getLoggedInUser();
        Assert::that($loggedInUser)->notNull();

        $record = $this->connection->fetchAssociative(
            'SELECT * FROM meetups WHERE meetupId = ?',
            [$request->getAttribute('id')]
        );

        if ($record === false) {
            return $this->responseFactory->createResponse(400);
        }

        [$date, $time] = explode(' ', Mapping::getString($record, 'scheduledFor'));

        $formErrors = [];
        $formData = [
            'scheduleForDate' => $date,
            'scheduleForTime' => $time,
        ];

        if ($request->getMethod() === 'POST') {
            $submittedData = $request->getParsedBody();
            Assertion::isArray($submittedData);

            $formData = array_merge($formData, $submittedData);

            $dateTime = DateTimeImmutable::createFromFormat(
                'Y-m-d H:i',
                $formData['scheduleForDate'] . ' ' . $formData['scheduleForTime']
            );
            if ($dateTime === false) {
                $formErrors['scheduleFor'][] = 'Invalid date/time';
            }

            if ($formErrors === []) {

                $this->application->rescheduleMeetup(
                    Mapping::getString($record, 'meetupId'),
                    $loggedInUser->userId()->asString(),
                    $formData['scheduleForDate'] . ' ' . $formData['scheduleForTime']
                );

                $this->session->addSuccessFlash('You have rescheduled the meetup');

                return new RedirectResponse($this->router->generateUri('list_meetups'));
            }
        }

        return new HtmlResponse(
            $this->renderer->render(
                'app::reschedule-meetup.html.twig',
                [
                    'formData' => $formData,
                    'formErrors' => $formErrors,
                    'meetupId' => $record['meetupId'],
                    'meetupName' => $record['name'],
                ]
            )
        );
    }
}
