<?php
declare(strict_types=1);

namespace App;

interface Outbox
{
    public function send(string $messageType, array $messageData): void;
}
