<?php

declare(strict_types=1);

namespace App;

use App\Entity\User;
use App\Entity\UserId;
use App\Entity\UserRepository;
use Assert\Assert;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use MeetupOrganizing\Application\RsvpForMeetup;
use MeetupOrganizing\Application\SignUp;
use MeetupOrganizing\Entity\MeetupId;
use MeetupOrganizing\Entity\Rsvp;
use MeetupOrganizing\Entity\RsvpRepository;
use MeetupOrganizing\Entity\ScheduledDate;
use MeetupOrganizing\Entity\UserHasRsvpd;
use MeetupOrganizing\MeetupWasScheduled;
use MeetupOrganizing\ViewModel\MeetupDetails;
use MeetupOrganizing\ViewModel\MeetupDetailsRepository;
use RuntimeException;

final class Application implements ApplicationInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly MeetupDetailsRepository $meetupDetailsRepository,
        private readonly EventDispatcher $eventDispatcher,
        private readonly Connection $connection,
        private readonly RsvpRepository $rsvpRepository,
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

    public function rsvpForMeetup(RsvpForMeetup $command): void
    {
        $statement = $this->connection
            ->createQueryBuilder()
            ->select('*')
            ->from('meetups')
            ->where('meetupId = :meetupId')
            ->setParameter('meetupId', $command->meetupId())
            ->execute();
        Assert::that($statement)->isInstanceOf(Statement::class);

        $record = $statement->fetchAssociative();

        if ($record === false) {
            throw new RuntimeException('Meetup not found');
        }

        $rsvp = Rsvp::create($command->meetupId(), $command->userId());
        $this->rsvpRepository->save($rsvp);

        $this->eventDispatcher->dispatch(
            new UserHasRsvpd($command->meetupId(), $command->userId(), $rsvp->rsvpId())
        );
    }

    public function scheduleMeetup(ScheduleMeetup $command): int
    {
        $record = [
            'organizerId' => $command->organizerId,
            'name' => $command->name,
            'description' => $command->description,
            'scheduledFor' => $command->scheduledFor,
            'wasCancelled' => 0,
        ];
        $this->connection->insert('meetups', $record);

        $meetupId = (int) $this->connection->lastInsertId();

        $this->eventDispatcher->dispatch(
            new MeetupWasScheduled(
                MeetupId::fromString((string) $meetupId),
                UserId::fromString($command->organizerId),
                ScheduledDate::fromString($command->scheduledFor),
            )
        );

        return $meetupId;
    }
}
