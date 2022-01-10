<?php
declare(strict_types=1);

namespace App\Handler;

use Assert\Assert;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use App\Entity\Rsvp;
use App\Entity\RsvpRepository;
use App\Entity\UserHasRsvpd;
use App\EventDispatcher;
use App\Session;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;

final class RsvpForMeetupHandler implements RequestHandlerInterface
{
    private Connection $connection;

    private Session $session;

    private RsvpRepository $rsvpRepository;

    private RouterInterface $router;
    private EventDispatcher $eventDispatcher;

    public function __construct(
        Connection $connection,
        Session $session,
        RsvpRepository $rsvpRepository,
        RouterInterface $router,
        EventDispatcher $eventDispatcher
    ) {
        $this->connection = $connection;
        $this->session = $session;
        $this->rsvpRepository = $rsvpRepository;
        $this->router = $router;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $postData = $request->getParsedBody();
        Assert::that($postData)->isArray();

        if (!isset($postData['meetupId'])) {
            throw new RuntimeException('Bad request');
        }

        $statement = $this->connection
            ->createQueryBuilder()
            ->select('*')
            ->from('meetups')
            ->where('meetupId = :meetupId')
            ->setParameter('meetupId', $postData['meetupId'])
            ->execute();
        Assert::that($statement)->isInstanceOf(Statement::class);

        $record = $statement->fetchAssociative();

        if ($record === false) {
            throw new RuntimeException('Meetup not found');
        }

        $rsvp = Rsvp::create(
            $postData['meetupId'],
            $this->session->getLoggedInUser()->userId()
        );
        $this->rsvpRepository->save($rsvp);

        $this->eventDispatcher->dispatch(
            new UserHasRsvpd($postData['meetupId'], $this->session->getLoggedInUser()->userId(), $rsvp->rsvpId())
        );

        return new RedirectResponse(
            $this->router->generateUri(
                'meetup_details',
                [
                    'id' => $postData['meetupId']
                ]
            )
        );
    }
}
