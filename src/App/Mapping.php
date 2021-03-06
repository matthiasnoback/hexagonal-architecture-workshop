<?php

declare(strict_types=1);

namespace App;

use Assert\Assert;

final class Mapping
{
    /**
     * @param array<string,mixed> $data
     */
    public static function getString(array $data, string $key): string
    {
        Assert::that($data)->keyExists($key);

        Assert::that($data[$key])->scalar();

        return (string) $data[$key];
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function getInt(array $data, string $key): int
    {
        Assert::that($data)->keyExists($key);
        Assert::that($data[$key])->integerish();

        return (int) $data[$key];
    }
}
