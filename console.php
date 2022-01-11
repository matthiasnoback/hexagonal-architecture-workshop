<?php

declare(strict_types=1);

use App\Cli\ConsoleApplication;
use Psr\Container\ContainerInterface;

require_once __DIR__ . '/vendor/autoload.php';

(function () {
    /** @var ContainerInterface $container */
    $container = require __DIR__ . '/config/container.php';

    $application = $container->get(ConsoleApplication::class);

    $application->run();
})();
