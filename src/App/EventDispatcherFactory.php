<?php

declare(strict_types=1);

namespace App;

use App\Entity\UserHasRsvpd;
use Assert\Assert;
use Psr\Container\ContainerInterface;

final class EventDispatcherFactory
{
    public function __invoke(ContainerInterface $container): EventDispatcher
    {
        $eventDispatcher = new ConfigurableEventDispatcher();

        $config = $container->get('config');
        Assert::that($config)->isArray();

        $eventListeners = $config['event_listeners'] ?? [];

        foreach ($eventListeners as $eventClass => $listeners) {
            foreach ($listeners as $listener) {
                $eventDispatcher->registerSpecificListener(
                    UserHasRsvpd::class,
                    function ($event) use ($container, $listener) {
                        [$listenerServiceId, $listenerMethod] = $listener;
                        $listener = $container->get($listenerServiceId);
                        $listener->{$listenerMethod}($event);
                    }
                );
            }
        }

        return $eventDispatcher;
    }
}
