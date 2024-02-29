<?php
declare(strict_types=1);

namespace MeetupOrganizing;

use Assert\Assert;
use Assert\Assertion;
use Billing\MeetupOrganizingApiForBilling;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;

final class MeetupOrganizingApiForBillingImplementation implements MeetupOrganizingApiForBilling
{
    public function __construct(
        private readonly Connection $connection
    )
    {

    }
    public function numberOfMeetupsActuallyHosted(string $organizerId, int $month, int $year): int
    {
        $firstDayOfMonth = DateTimeImmutable::createFromFormat('Y-m-d', $year . '-' . $month . '-1');
        Assert::that($firstDayOfMonth)->isInstanceOf(DateTimeImmutable::class);
        $lastDayOfMonth = $firstDayOfMonth->modify('last day of this month');

        // Load the data directly from the database
        $result = $this->connection->executeQuery(
            'SELECT COUNT(meetupId) as numberOfMeetups FROM meetups WHERE organizerId = :organizerId AND scheduledFor >= :firstDayOfMonth AND scheduledFor <= :lastDayOfMonth',
            [
                'organizerId' => $organizerId,
                'firstDayOfMonth' => $firstDayOfMonth->format('Y-m-d'),
                'lastDayOfMonth' => $lastDayOfMonth->format('Y-m-d'),
            ]
        );

        $record = $result->fetchAssociative();
        Assert::that($record)->isArray();
        Assertion::integerish($record['numberOfMeetups']);

        return (int) $record['numberOfMeetups'];
    }
}
