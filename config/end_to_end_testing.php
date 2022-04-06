<?php

declare(strict_types=1);

use App\ExternalEvents\ExternalEventPublisher;
use App\ExternalEvents\SynchronousExternalEventPublisherFactory;

return [
    'dependencies' => [
        'factories' => [
            ExternalEventPublisher::class => SynchronousExternalEventPublisherFactory::class
        ],
    ],
];
