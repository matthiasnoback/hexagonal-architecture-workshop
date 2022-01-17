<?php

declare(strict_types=1);

namespace MeetupOrganizing\Handler;

use App\EventDispatcher;
use App\Session;
use Assert\Assert;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use Laminas\Diactoros\Response\RedirectResponse;
use MeetupOrganizing\Entity\Rsvp;
use MeetupOrganizing\Entity\RsvpRepository;
use MeetupOrganizing\Entity\UserHasRsvpd;
use Mezzio\Router\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

final class RsvpForMeetupHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly Connection $connection,
        private readonly Session $session,
        private readonly RsvpRepository $rsvpRepository,
        private readonly RouterInterface $router,
        private readonly EventDispatcher $eventDispatcher
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $postData = $request->getParsedBody();
        Assert::that($postData)->isArray();

        if (! isset($postData['meetupId'])) {
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

        $user = $this->session->getLoggedInUser();
        Assert::that($user)->notNull();

        $rsvp = Rsvp::create($postData['meetupId'], $user->userId());
        $this->rsvpRepository->save($rsvp);

        $this->eventDispatcher->dispatch(
            new UserHasRsvpd($postData['meetupId'], $user->userId(), $rsvp->rsvpId())
        );

        return new RedirectResponse(
            $this->router->generateUri('meetup_details', [
                'id' => $postData['meetupId'],
            ])
        );
    }
}
