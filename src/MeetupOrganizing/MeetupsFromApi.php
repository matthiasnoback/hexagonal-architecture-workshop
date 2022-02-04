<?php
declare(strict_types=1);

namespace MeetupOrganizing;

use App\Mapping;
use Billing\Meetups;
use Assert\Assert;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

final class MeetupsFromApi implements Meetups
{
    public function __construct(
        private readonly ClientInterface $client,
        private readonly RequestFactoryInterface $requestFactory,
    )
    {

    }
    public function countScheduledMeetupsFor(int $year, int $month, string $organizerId): int
    {
        $response = $this->client->sendRequest(
            $this->requestFactory->createRequest(
                'GET',
                sprintf('/api/count-meetups/%s/%d/%d', $organizerId, $year, $month)
            )
        );
        $decodedData = json_decode($response->getBody()->getContents(), true);
        Assert::that($decodedData)->isArray();

        return Mapping::getInt($decodedData, 'numberOfMeetups');
    }
}
