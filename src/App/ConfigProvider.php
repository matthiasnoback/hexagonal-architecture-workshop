<?php

declare(strict_types=1);

namespace App;

use App\Entity\RsvpRepository;
use App\Entity\UserHasRsvpd;
use App\Entity\UserRepository;
use App\Handler\CancelMeetupHandler;
use App\Handler\ListMeetupsHandler;
use App\Handler\MeetupDetailsHandler;
use App\Handler\RsvpForMeetupHandler;
use App\Handler\ScheduleMeetupHandler;
use App\Handler\SwitchUserHandler;
use App\Twig\FlashExtension;
use App\Twig\UserExtension;
use Assert\Assert;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates' => $this->getTemplates(),
            'twig' => [
                'extensions' => [UserExtension::class, FlashExtension::class],
            ],
        ];
    }

    public function getDependencies(): array
    {
        return [
            'invokables' => [],
            'factories' => [
                ScheduleMeetupHandler::class => fn (ContainerInterface $container) => new ScheduleMeetupHandler(
                    $container->get(Session::class),
                    $container->get(TemplateRendererInterface::class),
                    $container->get(RouterInterface::class),
                    $container->get(Connection::class)
                ),
                MeetupDetailsHandler::class => fn (ContainerInterface $container) => new MeetupDetailsHandler(
                    $container->get(Connection::class),
                    $container->get(UserRepository::class),
                    $container->get(RsvpRepository::class),
                    $container->get(TemplateRendererInterface::class)
                ),
                CancelMeetupHandler::class => fn (ContainerInterface $container) => new CancelMeetupHandler(
                    $container->get(Connection::class),
                    $container->get(Session::class),
                    $container->get(RouterInterface::class)
                ),
                ListMeetupsHandler::class => fn (ContainerInterface $container) => new ListMeetupsHandler(
                    $container->get(Connection::class),
                    $container->get(TemplateRendererInterface::class)
                ),
                SwitchUserHandler::class => fn (ContainerInterface $container) => new SwitchUserHandler(
                    $container->get(UserRepository::class),
                    $container->get(Session::class)
                ),
                RsvpForMeetupHandler::class => fn (ContainerInterface $container) => new RsvpForMeetupHandler(
                    $container->get(Connection::class),
                    $container->get(Session::class),
                    $container->get(RsvpRepository::class),
                    $container->get(RouterInterface::class),
                    $container->get(EventDispatcher::class),
                ),
                EventDispatcher::class => function (ContainerInterface $container) {
                    $eventDispatcher = new ConfigurableEventDispatcher();

                    $eventDispatcher->registerSpecificListener(
                        UserHasRsvpd::class,
                        function () use ($container) {
                            /** @var Session $session */
                            $session = $container->get(Session::class);

                            $session->addSuccessFlash('You have successfully RSVP-ed to this meetup');
                        }
                    );

                    return $eventDispatcher;
                },
                Session::class => fn (ContainerInterface $container) => new Session($container->get(
                    UserRepository::class
                )),
                UserRepository::class => fn () => new UserRepository(),
                RsvpRepository::class => fn (ContainerInterface $container) => new RsvpRepository($container->get(
                    Connection::class
                )),
                Connection::class => function (ContainerInterface $container) {
                    $config = $container->get('config');
                    Assert::that($config)->isArray();

                    $dbFile = __DIR__ . '/../../var/app-' . ($config['environment'] ?? 'development') . '.sqlite';
                    $connection = DriverManager::getConnection([
                        'driver' => 'pdo_sqlite',
                        'path' => $dbFile,
                    ]);
                    (new SchemaManager($connection))->updateSchema();

                    return $connection;
                },
                UserExtension::class => fn (ContainerInterface $container) => new UserExtension($container->get(
                    Session::class
                ), $container->get(UserRepository::class)),
                FlashExtension::class => fn (ContainerInterface $container) => new FlashExtension($container->get(
                    Session::class
                )),
            ],
        ];
    }

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
