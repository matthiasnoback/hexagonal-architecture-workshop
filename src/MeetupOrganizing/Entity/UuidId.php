<?php

namespace MeetupOrganizing\Entity;

use Assert\Assert;
use Ramsey\Uuid\UuidInterface;

trait UuidId
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

    public static function fromUuid(UuidInterface $uuid): self
    {
        return new self($uuid->toString());
    }

    public function asString(): string
    {
        return $this->id;
    }
}
