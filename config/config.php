<?php

declare(strict_types=1);

use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\ConfigAggregator\PhpFileProvider;
use Mezzio\Helper\ConfigProvider;

$aggregator = new ConfigAggregator([
    \Mezzio\Twig\ConfigProvider::class,
    \Mezzio\Tooling\ConfigProvider::class,
    ConfigProvider::class,
    \Mezzio\Router\FastRouteRouter\ConfigProvider::class,
    \Laminas\HttpHandlerRunner\ConfigProvider::class,
    ConfigProvider::class,
    \Mezzio\ConfigProvider::class,
    \Mezzio\Router\ConfigProvider::class,
    \Laminas\Diactoros\ConfigProvider::class,
    // Default App module config
    App\ConfigProvider::class,
    // Load application config in a pre-defined order in such a way that local settings
    // overwrite global settings. (Loaded as first to last):
    //   - `global.php`
    //   - `*.global.php`
    //   - `local.php`
    //   - `*.local.php`
    new PhpFileProvider(realpath(__DIR__) . '/autoload/{{,*.}global,{,*.}local}.php'),
    new PhpFileProvider(realpath(__DIR__) . '/' . ($_ENV['APPLICATION_ENV'] ?? 'development') . '.php')
]);

return $aggregator->getMergedConfig();
