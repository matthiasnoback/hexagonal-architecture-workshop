<?php

declare(strict_types=1);

namespace MeetupOrganizing\Handler;

use App\ApplicationInterface;
use App\Session;
use Assert\Assert;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

final class CancelMeetupHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ApplicationInterface $application,
        private readonly Session $session,
        private readonly RouterInterface $router
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $loggedInUser = $this->session->getLoggedInUser();
        Assert::that($loggedInUser)->notNull();

        $parsedBody = $request->getParsedBody();
        Assert::that($parsedBody)->isArray();

        if (! isset($parsedBody['meetupId'])) {
            throw new RuntimeException('Bad request');
        }
        $meetupId = $parsedBody['meetupId'];

        $this->application->cancelMeetup($meetupId, $loggedInUser->userId()->asString());

        $this->session->addSuccessFlash('You have cancelled the meetup');

        return new RedirectResponse($this->router->generateUri('list_meetups'));
    }
}
