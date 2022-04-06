<?php
declare(strict_types=1);

namespace App;

use Assert\Assertion;

final class Json
{
    public static function encode(array $data): string
    {
        $encoded = json_encode($data, JSON_THROW_ON_ERROR);
        Assertion::string($encoded);

        return $encoded;
    }

    public static function decode(string $encoded): array
    {
        $decoded = json_decode($encoded, true, 512, JSON_THROW_ON_ERROR);
        Assertion::isArray($decoded);

        return $decoded;
    }
}
