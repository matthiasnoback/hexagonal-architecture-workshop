<?php

declare(strict_types=1);

namespace MeetupOrganizing\Handler;

use App\Mapping;
use Assert\Assert;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ApiCountMeetupsHandler implements RequestHandlerInterface
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $organizerId = $request->getAttribute('organizerId');
        Assert::that($organizerId)->string();

        $year = $request->getAttribute('year');
        Assert::that($year)->integerish();

        $month = $request->getAttribute('month');
        Assert::that($month)->integerish();

        $firstDayOfMonth = DateTimeImmutable::createFromFormat('Y-m-d', $year . '-' . $month . '-1');
        Assert::that($firstDayOfMonth)->isInstanceOf(DateTimeImmutable::class);
        $lastDayOfMonth = $firstDayOfMonth->modify('last day of this month');

        // Load the data directly from the database
        $record = $this->connection->fetchAssociative(
            'SELECT COUNT(meetupId) as numberOfMeetups FROM meetups WHERE organizerId = :organizerId AND scheduledFor >= :firstDayOfMonth AND scheduledFor <= :lastDayOfMonth',
            [
                'organizerId' => $organizerId,
                'firstDayOfMonth' => $firstDayOfMonth->format('Y-m-d'),
                'lastDayOfMonth' => $lastDayOfMonth->format('Y-m-d'),
            ]
        );
        Assert::that($record)->isArray();

        $numberOfMeetups = Mapping::getInt($record, 'numberOfMeetups');

        return new JsonResponse(
            [
                'organizerId' => $organizerId,
                'year' => (int) $year,
                'month' => (int) $month,
                'numberOfMeetups' => $numberOfMeetups
            ]
        );
    }
}
