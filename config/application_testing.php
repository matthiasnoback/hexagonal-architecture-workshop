<?php

declare(strict_types=1);

use App\ExternalEvents\ExternalEventPublisher;
use App\ExternalEvents\SynchronousExternalEventPublisher;
use Billing\MeetupRepository;
use Psr\Container\ContainerInterface;

return [
    'dependencies' => [
        'factories' => [
            ExternalEventPublisher::class => fn (ContainerInterface $container) => new SynchronousExternalEventPublisher(
                $container->get('external_event_consumers')
            ),
            MeetupRepository::class => fn () => new \Billing\FakeMeetupRepository(),
            // TODO define application test-specific factories here, which will override earlier service definitions
        ],
    ],
];
