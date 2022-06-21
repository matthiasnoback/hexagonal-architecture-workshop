<?php

declare(strict_types=1);

use App\ExternalEvents\ExternalEventPublisher;
use App\ExternalEvents\SynchronousExternalEventPublisher;
use App\Time\Clock;
use AppTest\TheFakeClock;
use Psr\Container\ContainerInterface;

return [
    'dependencies' => [
        'factories' => [
            ExternalEventPublisher::class => fn (ContainerInterface $container) => new SynchronousExternalEventPublisher(
                $container->get('external_event_consumers')
            ),
            Clock::class => fn () => new TheFakeClock()
        ],
    ],
];
