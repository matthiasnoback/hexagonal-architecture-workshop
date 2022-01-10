<?php

declare(strict_types=1);

use App\Handler\CancelMeetupHandler;
use App\Handler\ListMeetupsHandler;
use App\Handler\MeetupDetailsHandler;
use App\Handler\ScheduleMeetupHandler;
use Mezzio\Application;
use Mezzio\MiddlewareFactory;
use Psr\Container\ContainerInterface;

return static function (Application $app, MiddlewareFactory $factory, ContainerInterface $container): void {
    $app->route('/', App\Handler\HomePageHandler::class, ['GET'], 'home');
    $app->route('/api/ping', App\Handler\PingHandler::class, ['GET'], 'api.ping');
    $app->route('/schedule-meetup', ScheduleMeetupHandler::class, ['GET', 'POST'], 'schedule_meetup');
    $app->route('/meetup-details/{id:.+}', MeetupDetailsHandler::class, ['GET'], 'meetup_details');
    $app->route('/cancel-meetup', CancelMeetupHandler::class, ['POST'], 'cancel_meetup');
    $app->route('/list-meetups', ListMeetupsHandler::class, ['GET'], 'list_meetups');
};
