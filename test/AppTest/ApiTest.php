<?php

declare(strict_types=1);

namespace AppTest;

final class ApiTest extends AbstractBrowserTest
{
    public function testApiPing(): void
    {
        $this->browser->request('GET', '/api/ping');

        $jsonData = $this->browser->getInternalResponse()
            ->getContent();
        self::assertJson($jsonData);

        $decodedData = json_decode($jsonData, true);
        self::assertIsArray($decodedData);
        self::assertArrayHasKey('time', $decodedData);
    }
}
