<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Rsvp;
use App\Entity\RsvpRepository;
use App\Entity\UserId;
use App\Entity\UserRepository;
use Assert\Assert;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use RuntimeException;

final class MeetupDetailsRepository
{
    public function __construct(
        private readonly Connection $connection,
        private readonly UserRepository $userRepository,
        private readonly RsvpRepository $rsvpRepository
    ) {
    }

    public function getById(string $meetupId): MeetupDetails
    {
        $statement = $this->connection
            ->createQueryBuilder()
            ->select('*')
            ->from('meetups')
            ->where('meetupId = :meetupId')
            ->setParameter('meetupId', $meetupId)
            ->execute();
        Assert::that($statement)->isInstanceOf(Statement::class);

        $meetup = $statement->fetchAssociative();
        if ($meetup === false) {
            throw new RuntimeException('Meetup not found');
        }

        Assert::that($meetup['organizerId'])->string();
        $organizer = $this->userRepository->getById(UserId::fromString($meetup['organizerId']));
        Assert::that($meetup['meetupId'])->integer();
        $rsvps = $this->rsvpRepository->getByMeetupId((string) $meetup['meetupId']);
        $users = array_map(fn (Rsvp $rsvp) => $this->userRepository->getById($rsvp->userId()), $rsvps);

        return new MeetupDetails($meetup, $organizer, $users);
    }
}
