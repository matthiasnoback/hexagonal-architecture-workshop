<?php

declare(strict_types=1);

namespace App;

use App\Entity\RsvpRepository;
use App\Handler\CancelMeetupHandler;
use App\Handler\HomePageHandler;
use App\Handler\ListMeetupsHandler;
use App\Handler\MeetupDetailsHandler;
use App\Handler\ScheduleMeetupHandler;
use App\Twig\FlashExtension;
use App\Twig\UserExtension;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use App\Entity\UserRepository;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

/**
 * The configuration provider for the App module
 *
 * @see https://docs.laminas.dev/laminas-component-installer/
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates' => $this->getTemplates(),
            'twig' => [
                'extensions' => [
                    UserExtension::class,
                    FlashExtension::class
                ]
            ]
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies(): array
    {
        return [
            'invokables' => [
                Handler\PingHandler::class => Handler\PingHandler::class,
            ],
            'factories' => [
                Handler\HomePageHandler::class => function (ContainerInterface $container) {
                    return new HomePageHandler($container->get(TemplateRendererInterface::class));
                },
                ScheduleMeetupHandler::class => function (ContainerInterface $container) {
                    return new ScheduleMeetupHandler(
                        $container->get(Session::class),
                        $container->get(TemplateRendererInterface::class),
                        $container->get(RouterInterface::class),
                        $container->get(Connection::class)
                    );
                },
                MeetupDetailsHandler::class => function (ContainerInterface $container) {
                    return new MeetupDetailsHandler(
                        $container->get(Connection::class),
                        $container->get(UserRepository::class),
                        $container->get(RsvpRepository::class),
                        $container->get(TemplateRendererInterface::class)
                    );
                },
                CancelMeetupHandler::class => function (ContainerInterface $container) {
                    return new CancelMeetupHandler(
                        $container->get(Connection::class),
                        $container->get(Session::class),
                        $container->get(RouterInterface::class)
                    );
                },
                ListMeetupsHandler::class => function (ContainerInterface $container) {
                    return new ListMeetupsHandler(
                        $container->get(Connection::class),
                        $container->get(TemplateRendererInterface::class)
                    );
                },
                Session::class => function (ContainerInterface $container) {
                    return new Session($container->get(UserRepository::class));
                },
                UserRepository::class => function () {
                    return new UserRepository();
                },
                RsvpRepository::class => function (ContainerInterface $container) {
                    return new RsvpRepository($container->get(Connection::class));
                },
                Connection::class => function () {
                    return DriverManager::getConnection(
                        [
                            'driver' => 'pdo_sqlite',
                            'path' => __DIR__ . '/../../var/app.sqlite'
                        ]
                    );
                },
                SchemaManager::class => function (ContainerInterface $container) {
                    return new SchemaManager($container->get(Connection::class));
                },
                UserExtension::class => function (ContainerInterface $container) {
                    return new UserExtension($container->get(Session::class), $container->get(UserRepository::class));
                },
                FlashExtension::class => function (ContainerInterface $container) {
                    return new FlashExtension($container->get(Session::class));
                }
            ],
        ];
    }

    /**
     * Returns the templates configuration
     */
    public function getTemplates(): array
    {
        return [
            'paths' => [
                'app' => ['templates/app'],
                'error' => ['templates/error'],
                'layout' => ['templates/layout'],
            ],
        ];
    }
}
