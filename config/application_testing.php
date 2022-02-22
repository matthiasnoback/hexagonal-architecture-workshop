<?php

declare(strict_types=1);

use Billing\MeetupRepository;
use Billing\MeetupRepositoryForTesting;

return [
    'dependencies' => [
        'factories' => [
            MeetupRepository::class => fn () => new MeetupRepositoryForTesting()
        ],
    ],
];
