<?php

declare(strict_types=1);

use App\Handler\LoginHandler;
use App\Handler\LogoutHandler;
use App\Handler\SignUpHandler;
use App\Handler\SwitchUserHandler;
use Billing\Handler\CreateInvoiceHandler;
use Billing\Handler\DeleteInvoiceHandler;
use Billing\Handler\ListInvoicesHandler;
use Billing\Handler\ListOrganizersHandler;
use MeetupOrganizing\Handler\ApiCountMeetupsHandler;
use MeetupOrganizing\Handler\ApiPingHandler;
use MeetupOrganizing\Handler\CancelMeetupHandler;
use MeetupOrganizing\Handler\ListMeetupsHandler;
use MeetupOrganizing\Handler\MeetupDetailsHandler;
use MeetupOrganizing\Handler\RescheduleMeetupHandler;
use MeetupOrganizing\Handler\RsvpForMeetupHandler;
use MeetupOrganizing\Handler\ScheduleMeetupHandler;
use Mezzio\Application;
use Mezzio\MiddlewareFactory;
use Psr\Container\ContainerInterface;

return static function (Application $app, MiddlewareFactory $factory, ContainerInterface $container): void {
    $app->route('/schedule-meetup', ScheduleMeetupHandler::class, ['GET', 'POST'], 'schedule_meetup');
    $app->route('/meetup-details/{id:.+}', MeetupDetailsHandler::class, ['GET'], 'meetup_details');
    $app->route('/rsvp-for-meetup', RsvpForMeetupHandler::class, ['POST'], 'rsvp_for_meetup');
    $app->route('/reschedule-meetup/{id:.+}', RescheduleMeetupHandler::class, ['GET', 'POST'], 'reschedule_meetup');
    $app->route('/cancel-meetup', CancelMeetupHandler::class, ['POST'], 'cancel_meetup');
    $app->route('/', ListMeetupsHandler::class, ['GET'], 'list_meetups');
    $app->route('/sign-up', SignUpHandler::class, ['GET', 'POST'], 'sign_up');
    $app->route('/login', LoginHandler::class, ['GET', 'POST'], 'login');
    $app->route('/logout', LogoutHandler::class, ['POST'], 'logout');
    $app->route('/switch-user', SwitchUserHandler::class, ['POST'], 'switch_user');
    $app->route('/admin/list-organizers', ListOrganizersHandler::class, ['GET'], 'list_organizers');
    $app->route('/billing/list-invoices/{organizerId:.+}', ListInvoicesHandler::class, ['GET'], 'list_invoices');
    $app->route(
        '/billing/create-invoice/{organizerId:.+}',
        CreateInvoiceHandler::class,
        ['GET', 'POST'],
        'create_invoice'
    );
    $app->route(
        '/billing/delete-invoice/{organizerId:.+}/{invoiceId:.+}',
        DeleteInvoiceHandler::class,
        ['POST'],
        'delete_invoice'
    );
    $app->route('/api/ping', ApiPingHandler::class, ['GET'], 'api_ping');
    $app->route(
        '/api/count-meetups/{organizerId:.+}/{year:\d+}/{month:\d+}',
        ApiCountMeetupsHandler::class,
        ['GET'],
        'api_count_meetups'
    );
};
