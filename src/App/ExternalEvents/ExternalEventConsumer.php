<?php
declare(strict_types=1);

namespace App\ExternalEvents;

interface ExternalEventConsumer
{
    public function whenConsumerRestarted(): void;

    public function whenExternalEventReceived(ExternalEventReceived $event): void;
}
