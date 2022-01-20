<?php
declare(strict_types=1);

namespace Billing;

use App\Mapping;
use Assert\Assert;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

final class MeetupUsingApi implements MeetupInterface
{
    public function __construct(
        private readonly ClientInterface $client,
        private readonly RequestFactoryInterface $requestFactory,
    )
    {
    }

    public function countMeetups(int $year, int $month, string $organizerId): int
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
