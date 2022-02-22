<?php

declare(strict_types=1);

namespace App;

use App\Entity\User;
use App\Entity\UserRepository;
use Assert\Assert;
use Billing\MeetupRepository;
use Billing\NothingToInvoice;
use Billing\ViewModel\Invoice;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use MeetupOrganizing\Application\SignUp;
use MeetupOrganizing\Entity\ScheduledDate;
use MeetupOrganizing\ViewModel\MeetupDetails;
use MeetupOrganizing\ViewModel\MeetupDetailsRepository;
use MeetupOrganizing\ViewModel\MeetupSummary;

final class Application implements ApplicationInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly MeetupDetailsRepository $meetupDetailsRepository,
        private readonly Connection $connection,
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

        return $user->userId()
            ->asString();
    }

    public function meetupDetails(string $id): MeetupDetails
    {
        return $this->meetupDetailsRepository->getById($id);
    }

    public function scheduleMeetup(ScheduleMeetupCommand $command): int
    {
        $record = [
            'organizerId' => $command->organizerId(),
            'name' => $command->name(),
            'description' => $command->description(),
            'scheduledFor' => $command->scheduledFor()->asString(),
            'wasCancelled' => 0,
        ];
        $this->connection->insert('meetups', $record);

        return (int) $this->connection->lastInsertId();
    }

    public function listUpcomingMeetups(ListUpcomingMeetups $query): array
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('meetups')
            ->where('scheduledFor >= :now')
            ->setParameter('now', $query->date()
                ->format(ScheduledDate::DATE_TIME_FORMAT)
            )
            ->andWhere('wasCancelled = :wasNotCancelled')
            ->setParameter('wasNotCancelled', 0)
            ->execute();
        Assert::that($statement)->isInstanceOf(Statement::class);

        return array_map(
            fn (array $record) => new MeetupSummary(
                Mapping::getInt($record, 'meetupId'),
                Mapping::getString($record, 'name'),
                Mapping::getString($record, 'scheduledFor'),
            ),
            $statement->fetchAllAssociative()
        );
    }

    public function createInvoice(CreateInvoice $command): void
    {
        $numberOfMeetups = $this->meetupRepository->getNumberOfMeetups(
            $command->organizerId(),
            $command->year(),
            $command->month(),
        );

        if ($numberOfMeetups < 1) {
            throw new NothingToInvoice();
        }

        $invoiceAmount = $numberOfMeetups * 5;

        $this->connection->insert('invoices', [
            'organizerId' => $command->organizerId(),
            'amount' => number_format($invoiceAmount, 2),
            'year' => $command->year(),
            'month' => $command->month(),
        ]);
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
