<?php

declare(strict_types=1);

namespace App;

use App\Entity\User;
use App\Entity\UserId;
use App\Entity\UserRepository;
use App\Entity\UserType;
use Assert\Assert;
use Assert\Assertion;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use MeetupOrganizing\Application\RsvpForMeetup;
use MeetupOrganizing\Application\ScheduleMeetup;
use MeetupOrganizing\Application\SignUp;
use MeetupOrganizing\Entity\CouldNotFindMeetup;
use MeetupOrganizing\Entity\CouldNotFindRsvp;
use MeetupOrganizing\Entity\Meetup;
use MeetupOrganizing\Entity\MeetupId;
use MeetupOrganizing\Entity\MeetupRepository;
use MeetupOrganizing\Entity\Rsvp;
use MeetupOrganizing\Entity\RsvpRepository;
use MeetupOrganizing\Entity\RsvpWasCancelled;
use MeetupOrganizing\Entity\ScheduledDateTime;
use MeetupOrganizing\ViewModel\MeetupDetails;
use MeetupOrganizing\ViewModel\MeetupDetailsRepository;

final class Application implements ApplicationInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly MeetupDetailsRepository $meetupDetailsRepository,
        private readonly EventDispatcher $eventDispatcher,
        private readonly Connection $connection,
        private readonly RsvpRepository $rsvpRepository,
        private readonly MeetupRepository $meetupRepository,
        private readonly Clock $clock,
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

        $this->eventDispatcher->dispatchAll($rsvp->releaseEvents());
    }

    public function scheduleMeetup(ScheduleMeetup $command): string
    {
        $meetupId = $this->meetupRepository->nextIdentity();

        $organizer = $this->userRepository->getById(UserId::fromString($command->organizerId));
        Assertion::true($organizer->userTypeIs(UserType::Organizer));

        $meetup = Meetup::schedule(
            $meetupId,
            $organizer->userId(),
            $command->name,
            $command->description,
            ScheduledDateTime::fromString($command->scheduledFor),
            $this->clock->getCurrentTime(),
        );

        $this->meetupRepository->save($meetup);

        return $meetupId->asString();
    }

    public function cancelMeetup(string $meetupId, string $currentUserId): void
    {
        $meetup = $this->meetupRepository->getById(
            MeetupId::fromString($meetupId)
        );

        // Tell, Don't Ask
        $meetup->cancel(UserId::fromString($currentUserId));

        $this->meetupRepository->save($meetup);
    }

    public function rescheduleMeetup(string $meetupId, string $currentUserId, string $scheduledFor): void
    {
        $meetup = $this->meetupRepository->getById(
            MeetupId::fromString($meetupId)
        );

        // Tell, Don't Ask
        $meetup->reschedule(
            UserId::fromString($currentUserId),
            ScheduledDateTime::fromString($scheduledFor),
            $this->clock->getCurrentTime(),
        );

        $this->meetupRepository->save($meetup);
    }
}
