<?php

declare(strict_types=1);

use App\Clock;
use App\SystemClock;
use App\ExternalEvents\ExternalEventPublisher;
use App\ExternalEvents\SynchronousExternalEventPublisher;
use App\TestClock;
use Psr\Container\ContainerInterface;

return [
    'dependencies' => [
        'factories' => [
            ExternalEventPublisher::class => fn (ContainerInterface $container) => new SynchronousExternalEventPublisher(
                $container->get('external_event_consumers')
            ),
            Clock::class => fn () => new TestClock(
                new DateTimeImmutable($_SERVER['HTTP_X_CURRENT_TIME'] ?? throw new RuntimeException('Call setServerTime() first'))
            )
        ],
    ],
];
