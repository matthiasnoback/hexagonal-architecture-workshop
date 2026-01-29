<?php

declare(strict_types=1);

namespace App;

use App\Entity\User;
use App\Entity\UserId;
use App\Entity\UserRepository;
use Assert\Assert;
use Billing\ViewModel\Invoice;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use MeetupOrganizing\Api\MeetupRepository;
use MeetupOrganizing\Application\RsvpForMeetup;
use MeetupOrganizing\Application\ScheduleMeetup;
use MeetupOrganizing\Application\SignUp;
use MeetupOrganizing\Entity\CouldNotFindMeetup;
use MeetupOrganizing\Entity\CouldNotFindRsvp;
use MeetupOrganizing\Entity\Rsvp;
use MeetupOrganizing\Entity\RsvpRepository;
use MeetupOrganizing\Entity\RsvpWasCancelled;
use MeetupOrganizing\ViewModel\MeetupDetails;
use MeetupOrganizing\ViewModel\MeetupDetailsRepository;
use MeetupOrganizing\ViewModel\MeetupForList;

final class Application implements ApplicationInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly MeetupDetailsRepository $meetupDetailsRepository,
        private readonly EventDispatcher $eventDispatcher,
        private readonly Connection $connection,
        private readonly RsvpRepository $rsvpRepository,
        private readonly ClockInterface $clock,
        private readonly MeetupRepository $meetupRepository,
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

    public function scheduleMeetup(ScheduleMeetup $command): int
    {
        $organizer = $this->userRepository->getById($command->organizerId());

        $record = [
            'organizerId' => $organizer->userId()->asString(),
            'name' => $command->name,
            'description' => $command->description,
            'scheduledFor' => $command->scheduledFor()->format('Y-m-d H:i'),
            'wasCancelled' => 0,
        ];
        $this->connection->insert('meetups', $record);

        return (int) $this->connection->lastInsertId();
    }

    public function meetups(bool $showPastMeetups): array
    {
        $query = 'SELECT m.* FROM meetups m WHERE m.wasCancelled = 0';
        $parameters = [];

        if (!$showPastMeetups) {
            $query .= ' AND scheduledFor >= ?';
            $parameters[] = $this->clock->now()->format('Y-m-d H:i');
        }

        return array_map([MeetupForList::class, 'fromDatabaseRecord'],
            $this->connection->fetchAllAssociative($query, $parameters));
    }

    public function createInvoice(string $organizerId, int $year, int $month): bool
    {
        $numberOfMeetups = $this->meetupRepository->countMeetups($organizerId, (int)$year,  (int)$month);

        if ($numberOfMeetups > 0) {
            $invoiceAmount = $numberOfMeetups * 5;

            $this->connection->insert('invoices', [
                'organizerId' => $organizerId,
                'amount' => number_format($invoiceAmount, 2),
                'year' => $year,
                'month' => $month,
            ]);

            return true;
        } else {
            return false;
        }
    }

    public function listInvoices(string $organizerId): array
    {
        $records = $this->connection->fetchAllAssociative(
            'SELECT * FROM invoices WHERE organizerId = ?',
            [$organizerId]
        );
        return array_map(
            fn (array $record) => new Invoice(
                Mapping::getInt($record, 'invoiceId'),
                Mapping::getString($record, 'organizerId'),
                Mapping::getInt($record, 'month') . '/' . Mapping::getInt($record, 'year'),
                Mapping::getString($record, 'amount'),
            ),
            $records
        );
    }
}
