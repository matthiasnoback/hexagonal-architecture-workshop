<?php

declare(strict_types=1);

namespace App\Handler;

use Assert\Assert;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ApiCountMeetupsHandler implements RequestHandlerInterface
{
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
                'data' => [
                    'organizerId' => $organizerId,
                    'year' => (int) $year,
                    'month' => (int) $month,
                    'numberOfMeetups' => 1,
                ],
            ]
        );
    }
}
