<?php

declare(strict_types=1);

namespace MeetupOrganizing\Handler;

use Assert\Assert;
use Laminas\Diactoros\Response\JsonResponse;
use MeetupOrganizing\BillableMeetupsUsingDbal;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ApiCountMeetupsHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly BillableMeetupsUsingDbal $billableMeetups
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

        return new JsonResponse(
            [
                'organizerId' => $organizerId,
                'year' => (int) $year,
                'month' => (int) $month,
                'numberOfMeetups' => $this->billableMeetups->howManyBillableMeetupsDoesThisOrganizerHaveInTheGivenMonth(
                    $organizerId,
                    (int) $year,
                    (int) $month,
                ),
            ]
        );
    }
}
