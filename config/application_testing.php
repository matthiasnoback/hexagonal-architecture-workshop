<?php

declare(strict_types=1);

use Billing\Meetups;
use Billing\MeetupsForTesting;
use Psr\Container\ContainerInterface;

return [
    'dependencies' => [
        'factories' => [
            // TODO define application test-specific factories here, which will override earlier service definitions
            Meetups::class => fn (ContainerInterface $container) => new MeetupsForTesting()
        ],
    ],
];
