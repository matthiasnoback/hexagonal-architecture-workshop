<?php
declare(strict_types=1);

use AppTest\Billing\MeetupRepositoryStub;
use Billing\MeetupRepositoryInterface;

return [
    'dependencies' => [
        'factories' => [
            MeetupRepositoryInterface::class => fn () => new MeetupRepositoryStub()
        ]
    ]
];
