<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\PhpUnit\PhpUnitStrictFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(
        Option::PATHS,
        [
            __DIR__ . '/config',
            __DIR__ . '/public',
            __DIR__ . '/src',
            __DIR__ . '/test',
            __DIR__ . '/ecs.php',
            __DIR__ . '/rector.php',
            __DIR__ . '/console.php',
        ]
    );

    $parameters->set(Option::SKIP, [PhpUnitStrictFixer::class]);

    $containerConfigurator->import(SetList::CONTROL_STRUCTURES);
    $containerConfigurator->import(SetList::PSR_12);
    $containerConfigurator->import(SetList::COMMON);
    $containerConfigurator->import(SetList::SYMPLIFY);
};
