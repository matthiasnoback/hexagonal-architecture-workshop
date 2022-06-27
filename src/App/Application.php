<?php

declare(strict_types=1);

namespace App;

use App\Entity\User;
use App\Entity\UserId;
use App\Entity\UserRepository;
use App\Entity\UserType;
use Assert\Assert;
use Billing\MeetupDataForBillingInterface;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use MeetupOrganizing\Application\RsvpForMeetup;
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
        private readonly MeetupDataForBillingInterface $meetupDataForBilling,
    ) {
    }

    public function signUp(SignUp $command): string
    {
        $user = User::create(
            $this->userRepository->nextIdentity(),
            $command->name(),
            $command->emailAddress(),
            UserType::from($command->userType)
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

    public function scheduleMeetup(
        string $organizerId,
        string $name,
        string $description,
        string $scheduledFor,
    ): int
    {
        $record = [
            'organizerId' => $organizerId,
            'name' => $name,
            'description' => $description,
            'scheduledFor' => $scheduledFor,
            'wasCancelled' => 0,
        ];
        $this->connection->insert('meetups', $record);

        return (int) $this->connection->lastInsertId();
    }

    public function listMeetups(bool $showPastMeetups, string $now): array
    {
        $query = 'SELECT m.* FROM meetups m WHERE m.wasCancelled = 0';
        $parameters = [];

        if (!$showPastMeetups) {
            $query .= ' AND scheduledFor >= ?';
            $parameters[] = (new DateTimeImmutable($now))->format('Y-m-d H:i');
        }

        $meetups = $this->connection->fetchAllAssociative($query, $parameters);

        return array_map(
            fn (array $record) => new MeetupForList(
                Mapping::getString($record, 'meetupId'),
                Mapping::getString($record, 'name'),
                Mapping::getString($record, 'scheduledFor'),
                Mapping::getString($record, 'organizerId'),
            ),
            $meetups
        );
    }

    public function createInvoice(string $organizerId, int $year, int $month): void
    {
        $numberOfMeetups = $this->meetupDataForBilling->getNumberOfMeetups(
            $year,
            $month,
            $organizerId
        );

        if ($numberOfMeetups === 0) {
            throw new InvoiceNotRequired();
        }
        $invoiceAmount = $numberOfMeetups * 5;

        $this->connection->insert('invoices', [
            'organizerId' => $organizerId,
            'amount' => number_format($invoiceAmount, 2),
            'year' => $year,
            'month' => $month,
        ]);
    }
}
