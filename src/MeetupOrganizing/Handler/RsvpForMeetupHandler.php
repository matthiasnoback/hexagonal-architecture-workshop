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

final class RsvpForMeetupHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly Session $session,
        private readonly RouterInterface $router,
        private readonly ApplicationInterface $application,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $postData = $request->getParsedBody();
        Assert::that($postData)->isArray();

        if (! isset($postData['meetupId'])) {
            throw new RuntimeException('Bad request');
        }

        $user = $this->session->getLoggedInUser();
        Assert::that($user)->notNull();

        $this->application->rsvpForMeetup(
            (int) $postData['meetupId'],
            $user->userId()->asString()
        );

        return new RedirectResponse(
            $this->router->generateUri('meetup_details', [
                'id' => $postData['meetupId'],
            ])
        );
    }
}
