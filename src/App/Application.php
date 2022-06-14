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
use MeetupOrganizing\Entity\CouldNotFindMeetup;
use MeetupOrganizing\Entity\CouldNotFindRsvp;
use MeetupOrganizing\Entity\Meetup;
use MeetupOrganizing\Entity\Meetups;
use MeetupOrganizing\Entity\Rsvp;
use MeetupOrganizing\Entity\RsvpRepository;
use MeetupOrganizing\Entity\RsvpWasCancelled;
use MeetupOrganizing\ValueObjects\ScheduledDate;
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
        private readonly Meetups $meetups,
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

        $this->eventDispatcher->dispatch(new RsvpWasCancelled($rsvp->rsvpId()));
    }

    public function scheduleMeetup(ScheduleMeetup $command): string
    {
        $organizerId = $this->userRepository->exists($command->organizerId);

        $meetup = Meetup::schedule(
            $this->meetups->nextMeetupId(),
            $organizerId,
            $command->name,
            $command->description,
            ScheduledDate::create($command->scheduledDateTime),
            $this->clock
        );

        $this->meetups->save($meetup);

        return $meetup->meetupId()->asString();
    }

    public function cancelMeetup(string $meetupId, string $currentUserId): void
    {
        $meetup = $this->meetups->get($meetupId);

        $meetup->cancel(UserId::fromString($currentUserId));

        $this->meetups->save($meetup);
    }

    public function rescheduleMeetup(string $meetupId, string $currentUserId, string $dateAndTime): void
    {
        $meetup = $this->meetups->get($meetupId);

        $meetup->reschedule(
            UserId::fromString($currentUserId),
            ScheduledDate::create($dateAndTime),
            $this->clock
        );

        $this->meetups->save($meetup);
    }
}
