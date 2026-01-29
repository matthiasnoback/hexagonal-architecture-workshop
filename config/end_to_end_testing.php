<?php

declare(strict_types=1);

use App\ClockInterface;
use App\ExternalEvents\ExternalEventPublisher;
use App\ExternalEvents\SynchronousExternalEventPublisher;
use App\HttpHeaderClock;
use Psr\Container\ContainerInterface;

return [
    'dependencies' => [
        'factories' => [
            ExternalEventPublisher::class => fn (ContainerInterface $container) => new SynchronousExternalEventPublisher(
                $container->get('external_event_consumers')
            ),
            ClockInterface::class => fn () => new HttpHeaderClock(),
        ],
    ],
];
