<?php
declare(strict_types=1);

namespace Billing;

use Assert\Assert;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

final class CountMeetupsUsingApi implements CountMeetups
{
    public function __construct(
        private readonly ClientInterface $client,
        private readonly RequestFactoryInterface $requestFactory,
    )
    {
    }
    public function forOrganizer(int $year, int $month, string $organizerId): int
    {
        $response = $this->client->sendRequest(
            $this->requestFactory->createRequest(
                'GET',
                sprintf('/api/count-meetups/%s/%d/%d', $organizerId, $year, $month)
            )
        );
        $decodedData = json_decode($response->getBody()->getContents(), true);
        Assert::that($decodedData)->isArray();

        return $decodedData['numberOfMeetups'];
    }
}
