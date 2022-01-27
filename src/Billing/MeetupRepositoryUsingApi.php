<?php
declare(strict_types=1);

namespace Billing;

use Assert\Assert;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

final class MeetupRepositoryUsingApi implements MeetupRepositoryInterface
{
    public function __construct(
        private ClientInterface $client,
        private RequestFactoryInterface $requestFactory
    )
    {
    }

    public function countMeetupsPerMonth(InvoicePeriod $invoicePeriod, string $organizerId): int
    {
        $response = $this->client->sendRequest(
            $this->requestFactory->createRequest(
                'GET',
                sprintf(
                    '/api/count-meetups/%s/%d/%d',
                    $organizerId,
                    $invoicePeriod->year(),
                    $invoicePeriod->month()
                )
            )
        );
        $decodedData = json_decode($response->getBody()->getContents(), true);
        Assert::that($decodedData)->isArray();

        return $decodedData['numberOfMeetups'];
    }
}
