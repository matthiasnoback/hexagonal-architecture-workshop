<?php

declare(strict_types=1);

use App\Json;
use PHPUnit\Framework\TestCase;

final class JsonTest extends TestCase
{
    public function testEncodeAndDecodeAgain(): void
    {
        $data = [
            'foo' => 'bar',
        ];

        self::assertSame($data, Json::decode(Json::encode($data)));
    }
}
