<?php

declare(strict_types=1);

use App\EventDispatcher;
use App\ExternalEventPublisher;
use App\Outbox;
use App\OutboxForTesting;
use App\SynchronousExternalEventPublisher;
use Psr\Container\ContainerInterface;

return [
    'dependencies' => [
        'factories' => [
            ExternalEventPublisher::class => fn (ContainerInterface $container) => new SynchronousExternalEventPublisher(
                $container->get(EventDispatcher::class)
            ),
            Outbox::class => fn (ContainerInterface $container) => new OutboxForTesting(
                $container->get(ExternalEventPublisher::class)
            ),
        ],
    ],
];
