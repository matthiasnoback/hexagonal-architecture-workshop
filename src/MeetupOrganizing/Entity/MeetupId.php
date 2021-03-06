<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use Assert\Assert;

final class MeetupId
{
    private function __construct(
        private readonly string $id
    ) {
        Assert::that($id)->uuid();
    }

    public static function fromString(string $id): self
    {
        return new self($id);
    }

    public function asString(): string
    {
        return $this->id;
    }
}
