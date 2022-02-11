<?php

declare(strict_types=1);

namespace MeetupOrganizing\Handler;

use Assert\Assert;
use MeetupOrganizing\ViewModel\MeetupOrganizingMeetupCounts;
use DateTimeImmutable;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ApiCountMeetupsHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly MeetupOrganizingMeetupCounts $meetupCounts
    )
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

        $numberOfMeetups = $this->meetupCounts->getTotalNumberOfMeetups(
            $organizerId,
            $firstDayOfMonth,
            $lastDayOfMonth
        );

        return new JsonResponse(
            [
                'organizerId' => $organizerId,
                'year' => (int) $year,
                'month' => (int) $month,
                'numberOfMeetups' => $numberOfMeetups,
            ]
        );
    }
}
