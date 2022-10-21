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
            // TODO define application test-specific factories here, which will override earlier service definitions
            \Billing\CountMeetups::class => fn () => new \AppTest\CountMeetupsMock()
        ],
    ],
];
