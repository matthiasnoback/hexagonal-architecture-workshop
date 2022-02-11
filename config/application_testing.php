<?php

declare(strict_types=1);

use AppTest\MeetupCountsForTesting;
use Billing\MeetupCounts;

return [
    'dependencies' => [
        'factories' => [
            // TODO define application test-specific factories here, which will override earlier service definitions
            MeetupCounts::class => fn () => new MeetupCountsForTesting()
        ],
    ],
];
