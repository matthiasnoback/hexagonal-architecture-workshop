<?php
declare(strict_types=1);

namespace Shared;

interface PublishedExternalEvent
{
    public static function fromArray(array $eventData): static;

    public function toArray(): array;

    public static function eventType(): string;
}
