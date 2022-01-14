<?php

declare(strict_types=1);

namespace App;

use App\Cli\ConsoleApplication;
use App\Cli\SignUpCommand;
use App\Entity\RsvpRepository;
use App\Entity\UserHasRsvpd;
use App\Entity\UserRepository;
use App\Handler\CancelMeetupHandler;
use App\Handler\CreateInvoiceHandler;
use App\Handler\ListMeetupsHandler;
use App\Handler\ListOrganizersHandler;
use App\Handler\LoginHandler;
use App\Handler\LogoutHandler;
use App\Handler\MeetupDetailsHandler;
use App\Handler\RsvpForMeetupHandler;
use App\Handler\ScheduleMeetupHandler;
use App\Handler\SignUpHandler;
use App\Twig\SessionExtension;
use Doctrine\DBAL\Connection;
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
                'extensions' => [SessionExtension::class],
            ],
            'project_root_dir' => realpath(__DIR__ . '/../../'),
            'event_listeners' => [
                UserHasRsvpd::class => [[AddFlashMessage::class, 'whenUserHasRsvped']],
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
                LoginHandler::class => fn (ContainerInterface $container) => new LoginHandler(
                    $container->get(UserRepository::class),
                    $container->get(Session::class),
                    $container->get(TemplateRendererInterface::class)
                ),
                LogoutHandler::class => fn (ContainerInterface $container) => new LogoutHandler(
                    $container->get(Session::class),
                    $container->get(RouterInterface::class)
                ),
                SignUpHandler::class => fn (ContainerInterface $container) => new SignUpHandler(
                    $container->get(TemplateRendererInterface::class),
                    $container->get(ApplicationInterface::class),
                    $container->get(RouterInterface::class),
                    $container->get(Session::class),
                ),
                RsvpForMeetupHandler::class => fn (ContainerInterface $container) => new RsvpForMeetupHandler(
                    $container->get(Connection::class),
                    $container->get(Session::class),
                    $container->get(RsvpRepository::class),
                    $container->get(RouterInterface::class),
                    $container->get(EventDispatcher::class),
                ),
                ListOrganizersHandler::class => fn (ContainerInterface $container) => new ListOrganizersHandler(
                    $container->get(Connection::class),
                    $container->get(TemplateRendererInterface::class)
                ),
                CreateInvoiceHandler::class => fn (ContainerInterface $container) => new CreateInvoiceHandler(
                    $container->get(Connection::class),
                    $container->get(Session::class),
                    $container->get(RouterInterface::class),
                    $container->get(TemplateRendererInterface::class),
                ),
                AddFlashMessage::class => fn (ContainerInterface $container) => new AddFlashMessage($container->get(
                    Session::class
                )),
                ApplicationInterface::class => fn (ContainerInterface $container) => new Application(
                    $container->get(Connection::class)
                ),
                EventDispatcher::class => EventDispatcherFactory::class,
                Session::class => fn (ContainerInterface $container) => new Session($container->get(
                    UserRepository::class
                )),
                UserRepository::class => fn (ContainerInterface $container) => new UserRepository($container->get(
                    Connection::class
                )),
                RsvpRepository::class => fn (ContainerInterface $container) => new RsvpRepository($container->get(
                    Connection::class
                )),
                Connection::class => ConnectionFactory::class,
                SessionExtension::class => fn (ContainerInterface $container) => new SessionExtension($container->get(
                    Session::class
                )),
                ConsoleApplication::class => fn (ContainerInterface $container) => new ConsoleApplication(
                    $container
                ),
                SignUpCommand::class => fn (ContainerInterface $container) => new SignUpCommand($container->get(
                    ApplicationInterface::class
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
