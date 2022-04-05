<?php

declare(strict_types=1);

namespace App;

use App\Entity\User;
use App\Entity\UserId;
use App\Entity\UserRepository;
use Assert\Assert;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use MeetupOrganizing\Application\SignUp;
use MeetupOrganizing\Entity\Rsvp;
use MeetupOrganizing\Entity\RsvpRepository;
use MeetupOrganizing\Entity\UserHasRsvpd;
use MeetupOrganizing\ViewModel\MeetupDetails;
use MeetupOrganizing\ViewModel\MeetupDetailsRepository;
use RuntimeException;

final class Application implements ApplicationInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly MeetupDetailsRepository $meetupDetailsRepository,
        private readonly EventDispatcher $eventDispatcher,
        private readonly RsvpRepository $rsvpRepository,
        private readonly Connection $connection,
    ) {
    }

    public function signUp(SignUp $command): string
    {
        $user = User::create(
            $this->userRepository->nextIdentity(),
            $command->name(),
            $command->emailAddress(),
            $command->userType()
        );

        $this->userRepository->save($user);

        $this->eventDispatcher->dispatchAll(
            $user->releaseEvents()
        );

        return $user->userId()
            ->asString();
    }

    public function meetupDetails(string $id): MeetupDetails
    {
        return $this->meetupDetailsRepository->getById($id);
    }

    public function rsvpMeetup(string $meetupId, string $userId): void
    {
        // get user
        $user = $this->userRepository->getById(UserId::fromString($userId));

        // the meetup exists
        $statement = $this->connection
            ->createQueryBuilder()
            ->select('*')
            ->from('meetups')
            ->where('meetupId = :meetupId')
            ->setParameter('meetupId', $meetupId)
            ->execute();
        Assert::that($statement)->isInstanceOf(Statement::class);

        $record = $statement->fetchAssociative();

        if ($record === false) {
            throw new RuntimeException('Meetup not found');
        }

        $rsvp = Rsvp::create($meetupId, $user->userId());
        $this->rsvpRepository->save($rsvp);

        $this->eventDispatcher->dispatch(
            new UserHasRsvpd($meetupId, $user->userId(), $rsvp->rsvpId())
        );
    }
}
