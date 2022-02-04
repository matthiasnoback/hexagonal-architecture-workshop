<?php

declare(strict_types=1);

namespace App;

use App\Cli\ConsoleApplication;
use App\Cli\SignUpCommand;
use App\Entity\UserRepository;
use App\Entity\UserRepositoryUsingDbal;
use App\Handler\LoginHandler;
use App\Handler\LogoutHandler;
use App\Handler\SignUpHandler;
use App\Handler\SwitchUserHandler;
use App\Twig\SessionExtension;
use Billing\Handler\CreateInvoiceHandler;
use Billing\Handler\DeleteInvoiceHandler;
use Billing\Handler\ListInvoicesHandler;
use Billing\Meetups;
use MeetupOrganizing\MeetupsFromApi;
use MeetupOrganizing\MeetupsFromDatabase;
use Doctrine\DBAL\Connection;
use GuzzleHttp\Psr7\HttpFactory;
use Http\Adapter\Guzzle7\Client;
use MeetupOrganizing\Entity\RsvpRepository;
use MeetupOrganizing\Entity\UserHasRsvpd;
use MeetupOrganizing\Handler\ApiCountMeetupsHandler;
use MeetupOrganizing\Handler\CancelMeetupHandler;
use MeetupOrganizing\Handler\ListMeetupsHandler;
use Billing\Handler\ListOrganizersHandler;
use MeetupOrganizing\Handler\MeetupDetailsHandler;
use MeetupOrganizing\Handler\RsvpForMeetupHandler;
use MeetupOrganizing\Handler\ScheduleMeetupHandler;
use MeetupOrganizing\ViewModel\MeetupDetailsRepository;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

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
                    $container->get(ApplicationInterface::class)
                ),
                MeetupDetailsHandler::class => fn (ContainerInterface $container) => new MeetupDetailsHandler(
                    $container->get(MeetupDetailsRepository::class),
                    $container->get(TemplateRendererInterface::class)
                ),
                CancelMeetupHandler::class => fn (ContainerInterface $container) => new CancelMeetupHandler(
                    $container->get(Connection::class),
                    $container->get(Session::class),
                    $container->get(RouterInterface::class)
                ),
                ListMeetupsHandler::class => fn (ContainerInterface $container) => new ListMeetupsHandler(
                    $container->get(ApplicationInterface::class),
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
                SwitchUserHandler::class => fn (ContainerInterface $container) => new SwitchUserHandler(
                    $container->get(UserRepository::class),
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
                ListInvoicesHandler::class => fn (ContainerInterface $container) => new ListInvoicesHandler(
                    $container->get(ApplicationInterface::class),
                    $container->get(TemplateRendererInterface::class)
                ),
                CreateInvoiceHandler::class => fn (ContainerInterface $container) => new CreateInvoiceHandler(
                    $container->get(ApplicationInterface::class),
                    $container->get(Session::class),
                    $container->get(RouterInterface::class),
                    $container->get(TemplateRendererInterface::class),
                ),
                Meetups::class => fn (ContainerInterface $container) =>
                    new MeetupsFromApi(
                        $container->get(ClientInterface::class),
                        $container->get(RequestFactoryInterface::class),
                    ),
                DeleteInvoiceHandler::class => fn (ContainerInterface $container) => new DeleteInvoiceHandler(
                    $container->get(Connection::class),
                    $container->get(RouterInterface::class),
                ),
                AddFlashMessage::class => fn (ContainerInterface $container) => new AddFlashMessage($container->get(
                    Session::class
                )),
                ApplicationInterface::class => fn (ContainerInterface $container) => new Application(
                    $container->get(UserRepository::class),
                    $container->get(MeetupDetailsRepository::class),
                    $container->get(Connection::class),
                    $container->get(Meetups::class),
                ),
                EventDispatcher::class => EventDispatcherFactory::class,
                Session::class => fn (ContainerInterface $container) => new Session($container->get(
                    UserRepository::class
                )),
                UserRepository::class => fn (ContainerInterface $container) => new UserRepositoryUsingDbal(
                    $container->get(Connection::class)
                ),
                RsvpRepository::class => fn (ContainerInterface $container) => new RsvpRepository($container->get(
                    Connection::class
                )),
                MeetupDetailsRepository::class => fn (ContainerInterface $container) => new MeetupDetailsRepository(
                    $container->get(Connection::class)
                ),
                Connection::class => ConnectionFactory::class,
                SchemaManager::class => fn (ContainerInterface $container) => new SchemaManager($container->get(
                    Connection::class
                )),
                SessionExtension::class => fn (ContainerInterface $container) => new SessionExtension(
                    $container->get(Session::class),
                    $container->get(UserRepository::class)
                ),
                ConsoleApplication::class => fn (ContainerInterface $container) => new ConsoleApplication(
                    $container
                ),
                SignUpCommand::class => fn (ContainerInterface $container) => new SignUpCommand($container->get(
                    ApplicationInterface::class
                )),
                RequestFactoryInterface::class => fn () => new HttpFactory(),
                ClientInterface::class => fn () => Client::createWithConfig(
                    [
                        'base_uri' => getenv('API_BASE_URI') ?: null,
                    ]
                ),
                ApiCountMeetupsHandler::class => fn (ContainerInterface $container) => new ApiCountMeetupsHandler(
                    $container->get(Connection::class),
                ),
            ],
        ];
    }

    public function getTemplates(): array
    {
        return [
            'paths' => [
                'app' => ['templates/app'],
                'admin' => ['templates/admin'],
                'billing' => ['templates/billing'],
                'error' => ['templates/error'],
                'layout' => ['templates/layout'],
            ],
        ];
    }
}
