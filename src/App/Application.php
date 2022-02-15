<?php

declare(strict_types=1);

namespace App;

use App\Entity\User;
use App\Entity\UserRepository;
use Assert\Assert;
use Billing\BillableMeetups;
use Billing\InvoiceNotNeeded;
use Billing\ViewModel\Invoice;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use MeetupOrganizing\Application\SignUp;
use MeetupOrganizing\Entity\ScheduledDate;
use MeetupOrganizing\ViewModel\MeetupDetails;
use MeetupOrganizing\ViewModel\MeetupDetailsRepository;
use MeetupOrganizing\ViewModel\MeetupListing;

final class Application implements ApplicationInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly MeetupDetailsRepository $meetupDetailsRepository,
        private readonly Connection $connection,
        private readonly BillableMeetups $billableMeetups,
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

        return $user->userId()
            ->asString();
    }

    public function meetupDetails(string $id): MeetupDetails
    {
        return $this->meetupDetailsRepository->getById($id);
    }

    public function scheduleMeetup(ScheduleMeetup $command): int
    {
        $record = [
            'organizerId' => $command->organizerId(),
            'name' => $command->meetupName(),
            'description' => $command->meetupDescription(),
            'scheduledFor' => $command->scheduledForDate() . ' ' . $command->scheduledForTime(),
            'wasCancelled' => 0,
        ];
        $this->connection->insert('meetups', $record);

        return (int) $this->connection->lastInsertId();
    }

    public function listMeetups(): array
    {
        $now = new DateTimeImmutable();

        $statement = $this->connection->createQueryBuilder()
            ->select(['meetupId', 'name', 'scheduledFor'])
            ->from('meetups')
            ->where('scheduledFor >= :now')
            ->setParameter('now', $now->format(ScheduledDate::DATE_TIME_FORMAT))
            ->andWhere('wasCancelled = :wasNotCancelled')
            ->setParameter('wasNotCancelled', 0)
            ->execute();
        Assert::that($statement)->isInstanceOf(Statement::class);

        $upcomingMeetups = $statement->fetchAllAssociative();

        return array_map(
            fn (array $record) => new MeetupListing(
                Mapping::getInt($record, 'meetupId'),
                Mapping::getString($record, 'name'),
                Mapping::getString($record, 'scheduledFor'),
            ),
            $upcomingMeetups,
        );
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

    public function createInvoice(string $organizerId, int $year, int $month,): void
    {
        $numberOfMeetups = $this->billableMeetups->howManyBillableMeetupsDoesThisOrganizerHaveInTheGivenMonth(
            $organizerId,
            $year,
            $month,
        );

        if ($numberOfMeetups === 0) {
            throw new InvoiceNotNeeded();
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
