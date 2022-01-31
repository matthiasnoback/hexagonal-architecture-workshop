<?php
declare(strict_types=1);

namespace App;

final class ExternalEventReceived
{
    public function __construct(private string $messageType, private array $messageData)
    {

    }

    public function messageType(): string
    {
        return $this->messageType;
    }

    public function messageData(): array
    {
        return $this->messageData;
    }
}
