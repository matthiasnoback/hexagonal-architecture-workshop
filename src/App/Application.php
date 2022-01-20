<?php

declare(strict_types=1);

namespace App;

use App\Entity\User;
use App\Entity\UserRepository;
use Assert\Assert;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use MeetupOrganizing\Application\SignUp;
use MeetupOrganizing\Entity\ScheduledDate;
use MeetupOrganizing\ViewModel\Meetup;
use MeetupOrganizing\ViewModel\MeetupDetails;
use MeetupOrganizing\ViewModel\MeetupDetailsRepository;

final class Application implements ApplicationInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly MeetupDetailsRepository $meetupDetailsRepository,
        private readonly Connection $connection
    ) {
    }

    public function signUp(SignUp $command): void
    {
        $user = User::create(
            $this->userRepository->nextIdentity(),
            $command->name(),
            $command->emailAddress(),
            $command->userType()
        );

        $this->userRepository->save($user);
    }

    public function meetupDetails(string $id): MeetupDetails
    {
        return $this->meetupDetailsRepository->getById($id);
    }

    public function scheduleMeetup(
        string $name,
        string $description,
        string $scheduledFor,
        string $organizerId
    ): string
    {
        $record = [
            'organizerId' => $organizerId,
            'name' => $name,
            'description' => $description,
            'scheduledFor' => $scheduledFor,
            'wasCancelled' => 0,
        ];
        $this->connection->insert('meetups', $record);

        $lastInsertId = $this->connection->lastInsertId();
        Assert::that($lastInsertId)->notSame(false);

        return (string)$lastInsertId;
    }

    public function listUpcomingMeetups(): array
    {
        $now = new DateTimeImmutable();

        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('meetups')
            ->where('scheduledFor >= :now')
            ->setParameter('now', $now->format(ScheduledDate::DATE_TIME_FORMAT))
            ->andWhere('wasCancelled = :wasNotCancelled')
            ->setParameter('wasNotCancelled', 0)
            ->execute();
        Assert::that($statement)->isInstanceOf(Statement::class);

        $upcomingMeetups = $statement->fetchAllAssociative();

        return array_map(
            fn (array $record) => new Meetup(
                Mapping::getInt($record, 'meetupId'),
                Mapping::getString($record, 'name'),
                Mapping::getString($record, 'scheduledFor'),
            ),
            $upcomingMeetups
        );
    }
}
