<?php

declare(strict_types=1);

namespace App;

use App\Entity\CouldNotFindUser;
use App\Entity\User;
use App\Entity\UserId;
use App\Entity\UserRepository;
use Assert\Assert;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use MeetupOrganizing\Application\RsvpForMeetup;
use MeetupOrganizing\Application\SignUp;
use MeetupOrganizing\Entity\CouldNotFindMeetup;
use MeetupOrganizing\Entity\CouldNotFindRsvp;
use MeetupOrganizing\Entity\Meetup;
use MeetupOrganizing\Entity\MeetupId;
use MeetupOrganizing\Entity\MeetupRepository;
use MeetupOrganizing\Entity\Rsvp;
use MeetupOrganizing\Entity\RsvpRepository;
use MeetupOrganizing\Entity\RsvpWasCancelled;
use MeetupOrganizing\Entity\ScheduledDate;
use MeetupOrganizing\ViewModel\MeetupDetails;
use MeetupOrganizing\ViewModel\MeetupDetailsRepository;

final class Application implements ApplicationInterface
{
    public function __construct(
        private readonly UserRepository          $userRepository,
        private readonly MeetupDetailsRepository $meetupDetailsRepository,
        private readonly EventDispatcher         $eventDispatcher,
        private readonly Connection              $connection,
        private readonly RsvpRepository          $rsvpRepository,
        private readonly MeetupRepository        $meetupRepository,
    )
    {
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

        $this->eventDispatcher->dispatchAll($user->releaseEvents());

        return $user->userId()
            ->asString();
    }

    public function meetupDetails(string $id): MeetupDetails
    {
        return $this->meetupDetailsRepository->getById($id);
    }

    public function rsvpForMeetup(RsvpForMeetup $command): void
    {
        try {
            $rsvp = $this->rsvpRepository->getByMeetupAndUserId($command->meetupId(), $command->userId());

            $rsvp->yes();
        } catch (CouldNotFindRsvp) {
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
                throw CouldNotFindMeetup::withId($command->meetupId());
            }

            $rsvp = Rsvp::forMeetup(
                $this->rsvpRepository->nextIdentity(),
                $command->meetupId(),
                $command->userId()
            );
        }

        $this->rsvpRepository->save($rsvp);

        $this->eventDispatcher->dispatchAll($rsvp->releaseEvents());
    }

    public function cancelRsvp(string $meetupId, string $userId): void
    {
        $userId = UserId::fromString($userId);

        $rsvp = $this->rsvpRepository->getByMeetupAndUserId($meetupId, $userId);

        $rsvp->no();

        $this->rsvpRepository->save($rsvp);

        $this->eventDispatcher->dispatch(new RsvpWasCancelled($rsvp->rsvpId()));
    }

    public function scheduleMeeting(
        ScheduleMeeting $command
    ): string
    {
        // form validation happened?

        // TODO check if organizerId refers to an organizer
        // TODO check many more things, and throw exceptions

        // Domain: behind the port
        // Application: ports: port methods + command DTOs
        // Infrastructure: adapters
        // The Dependency Rule

        // Commands:
        // create entity
        // save entity
        // return id

        //Or:

        // load entity
        // modify entity
        // save entity

        // who deals with the model?
        // can we decouple from our own model?

        $organizer = $this->userRepository->getById(UserId::fromString($command->organizerId()));

        $meetupId = $this->meetupRepository->nextId();

        $meetup = Meetup::schedule(
            $meetupId,
            $organizer->userId(),
            $command->name(),
            $command->description(),
            ScheduledDate::fromString($command->scheduledFor()),
        );

        $this->meetupRepository->save($meetup);

        return $meetupId->asString();
    }

    public function listUpcomingMeetups(string $now, bool $showPastMeetups): array
    {
        $now = new DateTimeImmutable($now);

        $query = 'SELECT m.* FROM meetups m WHERE m.wasCancelled = 0';
        $parameters = [];

        if (!$showPastMeetups) {
            $query .= ' AND scheduledFor >= ?';
            $parameters[] = $now->format('Y-m-d H:i');
        }

        $meetups = $this->connection->fetchAllAssociative($query, $parameters);

        $meetupLists = [];
        foreach ($meetups as $meetupRecord) {
            $meetupLists[] = MeetupForList::createFromRecord($meetupRecord);
        }

        return $meetupLists;
    }

    public function cancelMeetup(string $meetupId, string $userId): void
    {
        // load
        $meetup = $this->meetupRepository->getById(MeetupId::fromString($meetupId));

        // modify
        $meetup->cancel();

        // save
        $this->meetupRepository->save($meetup);
    }

    public function rescheduleMeetup(string $meetupId, string $scheduleFor, string $organizerId): void
    {
        $meetup = $this->meetupRepository->getById(MeetupId::fromString($meetupId));

        $meetup->reschedule(ScheduledDate::fromString($scheduleFor), UserId::fromString($organizerId));

        $this->meetupRepository->save($meetup);
    }
}
