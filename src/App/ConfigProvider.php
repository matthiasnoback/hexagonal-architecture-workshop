<?php

declare(strict_types=1);

namespace App;

use App\Cli\ConsoleApplication;
use App\Cli\ConsumeEventsCommand;
use App\Cli\ExportUsersCommand;
use App\Cli\OutboxRelayCommand;
use App\Cli\SignUpCommand;
use App\Core\Time\Clock;
use App\Core\Time\ProductionClock;
use App\Entity\UserHasSignedUp;
use App\Entity\UserRepository;
use App\Entity\UserRepositoryUsingDbal;
use App\ExternalEvents\AsynchronousExternalEventPublisher;
use App\ExternalEvents\ExternalEventPublisher;
use App\ExternalEvents\PublishExternalEvent;
use App\Handler\LoginHandler;
use App\Handler\LogoutHandler;
use App\Handler\SignUpHandler;
use App\Handler\SwitchUserHandler;
use App\Twig\SessionExtension;
use Billing\Handler\CreateInvoiceHandler;
use Billing\Handler\DeleteInvoiceHandler;
use Billing\Handler\ListInvoicesHandler;
use Billing\Handler\ListOrganizersHandler;
use Billing\Projections\OrganizerProjection;
use Doctrine\DBAL\Connection;
use GuzzleHttp\Psr7\HttpFactory;
use Http\Adapter\Guzzle7\Client;
use Laminas\Diactoros\ResponseFactory;
use MeetupOrganizing\Entity\MeetupRepository;
use MeetupOrganizing\Entity\RsvpRepository;
use MeetupOrganizing\Entity\RsvpWasCancelled;
use MeetupOrganizing\Entity\UserHasRsvpd;
use MeetupOrganizing\Handler\ApiCountMeetupsHandler;
use MeetupOrganizing\Handler\CancelMeetupHandler;
use MeetupOrganizing\Handler\CancelRsvpHandler;
use MeetupOrganizing\Handler\ListMeetupsHandler;
use MeetupOrganizing\Handler\MeetupDetailsHandler;
use MeetupOrganizing\Handler\RescheduleMeetupHandler;
use MeetupOrganizing\Handler\RsvpForMeetupHandler;
use MeetupOrganizing\Handler\ScheduleMeetupHandler;
use MeetupOrganizing\Infrastructure\MeetupRepositoryUsingDbal;
use MeetupOrganizing\Infrastructure\RsvpRepositoryUsingDbal;
use MeetupOrganizing\MeetupRsvpCountRepository;
use MeetupOrganizing\UpdateRsvpCountListener;
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
                UserHasRsvpd::class => [
                    [AddFlashMessage::class, 'whenUserHasRsvped'],
                    [UpdateRsvpCountListener::class, 'whenUserHasRsvpd'],
                ],
                RsvpWasCancelled::class => [
                    [UpdateRsvpCountListener::class, 'whenRsvpWasCancelled'],
                ],
                UserHasSignedUp::class => [[PublishExternalEvent::class, 'whenUserHasSignedUp']],
            ],
        ];
    }

    public function getDependencies(): array
    {
        return [
            'invokables' => [],
            'factories' => [
                UpdateRsvpCountListener::class => fn (ContainerInterface $container) => new UpdateRsvpCountListener($container->get(MeetupRsvpCountRepository::class)),
                MeetupRsvpCountRepository::class => fn (ContainerInterface $container) => $container->get(MeetupRepositoryUsingDbal::class),
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
                    $container->get(ApplicationInterface::class),
                    $container->get(Session::class),
                    $container->get(RouterInterface::class)
                ),
                RescheduleMeetupHandler::class => fn (ContainerInterface $container) => new RescheduleMeetupHandler(
                    $container->get(Connection::class),
                    $container->get(Session::class),
                    $container->get(RouterInterface::class),
                    $container->get(ResponseFactory::class),
                    $container->get(TemplateRendererInterface::class),
                    $container->get(ApplicationInterface::class),
                ),
                ListMeetupsHandler::class => fn (ContainerInterface $container) => new ListMeetupsHandler(
                    $container->get(Connection::class),
                    $container->get(TemplateRendererInterface::class),
                    $container->get(Clock::class),
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
                CancelRsvpHandler::class => fn (ContainerInterface $container) => new CancelRsvpHandler(
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
                    $container->get(MeetupRepository::class),
                    $container->get(Clock::class),
                ),
                Clock::class => fn () => new ProductionClock(),
                MeetupRepository::class => fn (ContainerInterface $container) => $container->get(MeetupRepositoryUsingDbal::class),
                MeetupRepositoryUsingDbal::class => fn (ContainerInterface $container) => new MeetupRepositoryUsingDbal($container->get(Connection::class)),
                EventDispatcher::class => EventDispatcherFactory::class,
                Session::class => fn (ContainerInterface $container) => new Session($container->get(
                    UserRepository::class
                )),
                UserRepository::class => fn (ContainerInterface $container) => new UserRepositoryUsingDbal(
                    $container->get(Connection::class)
                ),
                RsvpRepository::class => fn (ContainerInterface $container) => new RsvpRepositoryUsingDbal(
                    $container->get(
                    Connection::class
                )
                ),
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
                    $container->get('external_event_consumers'),
                ),
                OutboxRelayCommand::class => fn (ContainerInterface $container) => new OutboxRelayCommand(
                    $container->get(Connection::class),
                ),
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
                'external_event_consumers' => fn (ContainerInterface $container) => [
                    $container->get(OrganizerProjection::class),
                ],
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
