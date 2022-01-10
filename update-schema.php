<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

(function () {
    /** @var \Psr\Container\ContainerInterface $container */
    $container = require __DIR__ . '/config/container.php';

    /** @var \App\SchemaManager $schemaManager */
    $schemaManager = $container->get(\App\SchemaManager::class);

    $schemaManager->updateSchema();
})();
