<?php
declare(strict_types=1);

namespace App\Handler;

use Assert\Assert;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use App\Entity\Rsvp;
use App\Entity\RsvpRepository;
use App\Entity\UserId;
use App\Entity\UserRepository;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;
use Mezzio\Template\TemplateRendererInterface;

final class MeetupDetailsHandler implements RequestHandlerInterface
{
    private Connection $connection;

    private UserRepository $userRepository;

    private TemplateRendererInterface $renderer;

    private RsvpRepository $rsvpRepository;

    public function __construct(
        Connection $connection,
        UserRepository $userRepository,
        RsvpRepository $rsvpRepository,
        TemplateRendererInterface $renderer
    ) {
        $this->connection = $connection;
        $this->renderer = $renderer;
        $this->userRepository = $userRepository;
        $this->rsvpRepository = $rsvpRepository;
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
        $rsvps = $this->rsvpRepository->getByMeetupId((string)$meetup['meetupId']);
        $users = array_map(
            function (Rsvp $rsvp) {
                return $this->userRepository->getById($rsvp->userId());
            },
            $rsvps
        );

        return new HtmlResponse(
            $this->renderer->render(
                'app::meetup-details.html.twig',
                [
                    'meetup' => $meetup,
                    'organizer' => $organizer,
                    'attendees' => $users
                ])
        );
    }
}
