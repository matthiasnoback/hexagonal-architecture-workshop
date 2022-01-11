<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Set\ValueObject\LevelSetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PATHS, [
        __DIR__ . '/config',
        __DIR__ . '/public',
        __DIR__ . '/src',
        // No /test unfortunately, because Panther declares traits in a quasi-dynamic way
        __DIR__ . '/ecs.php',
        __DIR__ . '/rector.php',
    ]);

    $parameters->set(Option::AUTO_IMPORT_NAMES, true);

    $containerConfigurator->import(LevelSetList::UP_TO_PHP_81);
};
