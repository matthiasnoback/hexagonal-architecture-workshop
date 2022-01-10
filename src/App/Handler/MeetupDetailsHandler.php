<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Rsvp;
use App\Entity\RsvpRepository;
use App\Entity\UserId;
use App\Entity\UserRepository;
use Assert\Assert;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

final class MeetupDetailsHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly Connection $connection,
        private readonly UserRepository $userRepository,
        private readonly RsvpRepository $rsvpRepository,
        private readonly TemplateRendererInterface $renderer
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $statement = $this->connection
            ->createQueryBuilder()
            ->select('*')
            ->from('meetups')
            ->where('meetupId = :meetupId')
            ->setParameter('meetupId', $request->getAttribute('id'))
            ->execute();
        Assert::that($statement)->isInstanceOf(Statement::class);

        $meetup = $statement->fetchAssociative();
        if ($meetup === false) {
            throw new RuntimeException('Meetup not found');
        }

        Assert::that($meetup['organizerId'])->integer();
        $organizer = $this->userRepository->getById(UserId::fromInt($meetup['organizerId']));
        Assert::that($meetup['meetupId'])->integer();
        $rsvps = $this->rsvpRepository->getByMeetupId((string) $meetup['meetupId']);
        $users = array_map(fn (Rsvp $rsvp) => $this->userRepository->getById($rsvp->userId()), $rsvps);

        return new HtmlResponse(
            $this->renderer->render(
                'app::meetup-details.html.twig',
                [
                    'meetup' => $meetup,
                    'organizer' => $organizer,
                    'attendees' => $users,
                ]
            )
        );
    }
}
