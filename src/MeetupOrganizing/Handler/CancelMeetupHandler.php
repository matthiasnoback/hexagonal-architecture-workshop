<?php

declare(strict_types=1);

namespace MeetupOrganizing\Handler;

use App\EventDispatcher;
use App\Session;
use Assert\Assert;
use Doctrine\DBAL\Connection;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

final class CancelMeetupHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly Connection $connection,
        private readonly Session $session,
        private readonly RouterInterface $router,
        private readonly EventDispatcher $eventDispatcher,
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

        $numberOfAffectedRows = $this->connection->update(
            'meetups',
            [
                'wasCancelled' => 1,
            ],
            [
                'meetupId' => $meetupId,
                'organizerId' => $loggedInUser->userId()
                    ->asString(),
            ]
        );

        $this->eventDispatcher->dispatch(
            new MeetupWasCancelled($loggedInUser->userId(), $meetupId)
        );

        if ($numberOfAffectedRows > 0) {
            $this->session->addSuccessFlash('You have cancelled the meetup');
        }

        return new RedirectResponse($this->router->generateUri('list_meetups'));
    }
}
