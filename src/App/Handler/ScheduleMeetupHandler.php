<?php
declare(strict_types=1);

namespace App\Handler;

use App\Session;
use Assert\Assert;
use Doctrine\DBAL\Connection;
use Exception;
use App\Entity\ScheduledDate;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ScheduleMeetupHandler implements RequestHandlerInterface
{
    private Session $session;

    private TemplateRendererInterface $renderer;

    private RouterInterface $router;

    private Connection $connection;

    public function __construct(
        Session $session,
        TemplateRendererInterface $renderer,
        RouterInterface $router,
        Connection $connection
    ) {
        $this->session = $session;
        $this->renderer = $renderer;
        $this->router = $router;
        $this->connection = $connection;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $formErrors = [];
        $formData = [
            // This is a nice place to set some defaults
            'scheduleForTime' => '20:00'
        ];

        if ($request->getMethod() === 'POST') {
            $formData = $request->getParsedBody();
            Assert::that($formData)->isArray();

            if (empty($formData['name'])) {
                $formErrors['name'][] = 'Provide a name';
            }
            if (empty($formData['description'])) {
                $formErrors['description'][] = 'Provide a description';
            }
            try {
                ScheduledDate::fromString(
                    $formData['scheduleForDate'] . ' ' . $formData['scheduleForTime']
                );
            } catch (Exception $exception) {
                $formErrors['scheduleFor'][] = 'Invalid date/time';
            }

            if (empty($formErrors)) {
                $record = [
                    'organizerId' => $this->session->getLoggedInUser()->userId()->asInt(),
                    'name' => $formData['name'],
                    'description' => $formData['description'],
                    'scheduledFor' => $formData['scheduleForDate'] . ' ' . $formData['scheduleForTime'],
                    'wasCancelled' => 0
                ];
                $this->connection->insert('meetups', $record);

                $meetupId = (int)$this->connection->lastInsertId();

                $this->session->addSuccessFlash('Your meetup was scheduled successfully');

                return new RedirectResponse(
                    $this->router->generateUri(
                        'meetup_details',
                        [
                            'id' => $meetupId
                        ]
                    )
                );
            }
        }

        return new HtmlResponse(
            $this->renderer->render(
                'app::schedule-meetup.html.twig',
                [
                    'formData' => $formData,
                    'formErrors' => $formErrors
                ]
            )
        );
    }
}
