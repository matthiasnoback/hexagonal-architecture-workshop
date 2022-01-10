<?php

declare(strict_types=1);

use Mezzio\Container\WhoopsErrorResponseGeneratorFactory;
use Mezzio\Container\WhoopsFactory;
use Mezzio\Container\WhoopsPageHandlerFactory;

use Mezzio\Middleware\ErrorResponseGenerator;

return [
    // Provides application-wide services.
    // We recommend using fully-qualified class names whenever possible as
    // service names.
    'dependencies' => [
        // Use 'aliases' to alias a service name to another service. The
        // key is the alias name, the value is the service to which it points.
        'aliases' => [
            // Fully\Qualified\ClassOrInterfaceName::class => Fully\Qualified\ClassName::class,
        ],
        // Use 'invokables' for constructor-less services, or services that do
        // not require arguments to the constructor. Map a service name to the

        'invokables' => [
            // Fully\Qualified\InterfaceName::class => Fully\Qualified\ClassName::class,
        ],
        // Use 'factories' for services provided by callbacks/factory classes.
        'factories' => [
            ErrorResponseGenerator::class => WhoopsErrorResponseGeneratorFactory::class,
            'Mezzio\Whoops' => WhoopsFactory::class,
            'Mezzio\WhoopsPageHandler' => WhoopsPageHandlerFactory::class,
        ],
        // Fully\Qualified\ClassName::class => Fully\Qualified\FactoryName::class,
    ],
    'whoops' => [
        'json_exceptions' => [
            'display' => true,
            'show_trace' => true,
            'ajax_only' => true,
        ],
    ],
];
