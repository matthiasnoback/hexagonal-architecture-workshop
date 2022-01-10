<?php

declare(strict_types=1);

use App\Handler\CancelMeetupHandler;
use App\Handler\ListMeetupsHandler;
use App\Handler\MeetupDetailsHandler;
use App\Handler\RsvpForMeetupHandler;
use App\Handler\ScheduleMeetupHandler;
use App\Handler\SwitchUserHandler;
use Mezzio\Application;
use Mezzio\MiddlewareFactory;
use Psr\Container\ContainerInterface;

return static function (Application $app, MiddlewareFactory $factory, ContainerInterface $container): void {
    $app->route('/schedule-meetup', ScheduleMeetupHandler::class, ['GET', 'POST'], 'schedule_meetup');
    $app->route('/meetup-details/{id:.+}', MeetupDetailsHandler::class, ['GET'], 'meetup_details');
    $app->route('/rsvp-for-meetup', RsvpForMeetupHandler::class, ['POST'], 'rsvp_for_meetup');
    $app->route('/cancel-meetup', CancelMeetupHandler::class, ['POST'], 'cancel_meetup');
    $app->route('/', ListMeetupsHandler::class, ['GET'], 'list_meetups');
    $app->route('/switch-user', SwitchUserHandler::class, ['POST'], 'switch_user');
};
