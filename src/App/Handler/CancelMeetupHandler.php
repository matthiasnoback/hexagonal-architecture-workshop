<?php
declare(strict_types=1);

namespace App\Handler;

use Assert\Assert;
use Doctrine\DBAL\Connection;
use App\Session;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;

final class CancelMeetupHandler implements RequestHandlerInterface
{
    private Connection $connection;

    private Session $session;

    private RouterInterface $router;

    public function __construct(
        Connection $connection,
        Session $session,
        RouterInterface $router
    ) {
        $this->connection = $connection;
        $this->session = $session;
        $this->router = $router;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();
        Assert::that($parsedBody)->isArray();

        if (!isset($parsedBody['meetupId'])) {
            throw new RuntimeException('Bad request');
        }
        $meetupId = $parsedBody['meetupId'];

        $numberOfAffectedRows = $this->connection->update(
            'meetups',
            [
                'wasCancelled' => 1
            ],
            [
                'meetupId' => $meetupId,
                'organizerId' => $this->session->getLoggedInUser()->userId()->asInt()
            ]
        );

        if ($numberOfAffectedRows > 0) {
            $this->session->addSuccessFlash('You have cancelled the meetup');
        }

        return new RedirectResponse(
            $this->router->generateUri('list_meetups')
        );
    }
}
