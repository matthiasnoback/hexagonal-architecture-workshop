<?php

declare(strict_types=1);

use App\SchemaManager;
use Psr\Container\ContainerInterface;

require __DIR__ . '/vendor/autoload.php';

(function () {
    /** @var ContainerInterface $container */
    $container = require __DIR__ . '/config/container.php';

    /** @var SchemaManager $schemaManager */
    $schemaManager = $container->get(SchemaManager::class);

    $schemaManager->updateSchema();
})();
