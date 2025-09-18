<?php

declare(strict_types=1);

use App\Clock;
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
            Clock::class => fn (ContainerInterface $container) => new TestClock(
                new DateTimeImmutable('2023-09-18 11:43:00')
            ),
            // TODO define application test-specific factories here, which will override earlier service definitions
        ],
    ],
];
