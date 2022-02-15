<?php

declare(strict_types=1);

use AppTest\Integration\BillableMeetupsForTest;
use Billing\BillableMeetups;

return [
    'dependencies' => [
        'factories' => [
            BillableMeetups::class => fn () => new BillableMeetupsForTest()
        ],
    ],
];
