<?php

declare(strict_types=1);

namespace App;

use App\Cli\ConsoleApplication;
use App\Cli\ExportUsersCommand;
use App\Cli\OutboxRelayCommand;
use App\Cli\SignUpCommand;
use App\Entity\UserHasSignedUp;
use App\Entity\UserRepository;
use App\Entity\UserRepositoryUsingDbal;
use App\Handler\LoginHandler;
use App\Handler\LogoutHandler;
use App\Handler\SignUpHandler;
use App\Handler\SwitchUserHandler;
use App\Twig\SessionExtension;
use App\Cli\ConsumeEventsCommand;
use Billing\Handler\CreateInvoiceHandler;
use Billing\Handler\DeleteInvoiceHandler;
use Billing\Handler\ListInvoicesHandler;
use Billing\Projections\MeetupProjection;
use Billing\Projections\OrganizerProjection;
use Doctrine\DBAL\Connection;
use GuzzleHttp\Psr7\HttpFactory;
use Http\Adapter\Guzzle7\Client;
use MeetupOrganizing\Application\RsvpOrganizerForMeetup;
use MeetupOrganizing\BillingMeetups;
use MeetupOrganizing\Entity\MeetupWasCancelled;
use MeetupOrganizing\Entity\MeetupWasScheduled;
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
use TailEventStream\Consumer;
use TailEventStream\Producer;

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
                UserHasSignedUp::class => [[PublishExternalEvent::class, 'whenUserHasSignedUp']],
                ConsumerRestarted::class => [
                    [OrganizerProjection::class, 'whenConsumerRestarted'],
                    [MeetupProjection::class, 'whenConsumerRestarted'],
                ],
                ExternalEventReceived::class => [
                    [OrganizerProjection::class, 'whenExternalEventReceived'],
                    [MeetupProjection::class, 'whenExternalEventReceived'],
                ],
                MeetupWasScheduled::class => [
                    [RsvpOrganizerForMeetup::class, 'whenMeetupWasScheduled'],
                    [PublishExternalEvent::class, 'whenMeetupWasScheduled'],
                ],
                MeetupWasCancelled::class => [
                    [PublishExternalEvent::class, 'whenMeetupWasCancelled'],
                ]
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
                    $container->get(Connection::class),
                    $container->get(EventDispatcher::class),
                ),
                RsvpOrganizerForMeetup::class => fn (ContainerInterface $container) => new RsvpOrganizerForMeetup(
                    $container->get(ApplicationInterface::class)
                ),
                MeetupProjection::class => fn (ContainerInterface $container) => new MeetupProjection(
                    $container->get(Connection::class)
                ),
                MeetupDetailsHandler::class => fn (ContainerInterface $container) => new MeetupDetailsHandler(
                    $container->get(MeetupDetailsRepository::class),
                    $container->get(TemplateRendererInterface::class)
                ),
                CancelMeetupHandler::class => fn (ContainerInterface $container) => new CancelMeetupHandler(
                    $container->get(Connection::class),
                    $container->get(Session::class),
                    $container->get(RouterInterface::class),
                    $container->get(EventDispatcher::class),
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
                    $container->get(Session::class),
                    $container->get(RouterInterface::class),
                    $container->get(ApplicationInterface::class),
                ),
                ListOrganizersHandler::class => fn (ContainerInterface $container) => new ListOrganizersHandler(
                    $container->get(Connection::class),
                    $container->get(TemplateRendererInterface::class)
                ),
                ListInvoicesHandler::class => fn (ContainerInterface $container) => new ListInvoicesHandler(
                    $container->get(Connection::class),
                    $container->get(TemplateRendererInterface::class)
                ),
                CreateInvoiceHandler::class => fn (ContainerInterface $container) => new CreateInvoiceHandler(
                    $container->get(Connection::class),
                    $container->get(Session::class),
                    $container->get(RouterInterface::class),
                    $container->get(TemplateRendererInterface::class)
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
                    $container->get(EventDispatcher::class),
                    $container->get(Connection::class),
                    $container->get(RsvpRepository::class),
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
                ExportUsersCommand::class => fn () => new ExportUsersCommand(),
                ConsumeEventsCommand::class => fn (ContainerInterface $container) => new ConsumeEventsCommand(
                    $container->get(Consumer::class),
                    $container->get(EventDispatcher::class),
                ),
                OutboxRelayCommand::class => fn () => new OutboxRelayCommand(),
                OrganizerProjection::class => fn (ContainerInterface $container) => new OrganizerProjection(
                    $container->get(Connection::class),
                ),
                RequestFactoryInterface::class => fn () => new HttpFactory(),
                ClientInterface::class => fn () => Client::createWithConfig(
                    [
                        'base_uri' => getenv('API_BASE_URI') ?: null,
                    ]
                ),
                PublishExternalEvent::class => fn (ContainerInterface $container) => new PublishExternalEvent(
                    $container->get(ExternalEventPublisher::class),
                ),
                ExternalEventPublisher::class => fn (ContainerInterface $container) => new AsynchronousExternalEventPublisher(
                    $container->get(Producer::class)
                ),
                ApiCountMeetupsHandler::class => fn () => new ApiCountMeetupsHandler(),
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
