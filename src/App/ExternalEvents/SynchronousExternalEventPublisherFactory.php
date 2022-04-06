<?php
declare(strict_types=1);

namespace App\ExternalEvents;

use Assert\Assert;
use Psr\Container\ContainerInterface;

final class SynchronousExternalEventPublisherFactory
{
    public function __invoke(ContainerInterface $container): SynchronousExternalEventPublisher
    {
        $config = $container->get('config');
        Assert::that($config)->isArray();

        $serviceIds = $config['external_event_consumers'] ?? [];

        return new SynchronousExternalEventPublisher($container, $serviceIds);
    }
}
