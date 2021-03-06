<?php

declare(strict_types=1);

use App\ExternalEvents\ExternalEventPublisher;
use App\ExternalEvents\SynchronousExternalEventPublisher;
use Psr\Container\ContainerInterface;

return [
    'dependencies' => [
        'factories' => [
            ExternalEventPublisher::class => fn (ContainerInterface $container) => new SynchronousExternalEventPublisher(
                $container->get('external_event_consumers')
            ),
        ],
    ],
];
