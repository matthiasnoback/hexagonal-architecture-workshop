<?php

declare(strict_types=1);

namespace App;

use App\Entity\User;
use App\Entity\UserRepository;
use Assert\Assert;
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
}
