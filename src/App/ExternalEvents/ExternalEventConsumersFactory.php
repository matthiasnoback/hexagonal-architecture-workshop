<?php

declare(strict_types=1);

namespace App\ExternalEvents;

use Assert\Assert;
use Psr\Container\ContainerInterface;

final class ExternalEventConsumersFactory
{
    /**
     * @return array<ExternalEventConsumer>
     */
    public function __invoke(ContainerInterface $container): array
    {
        $config = $container->get('config');
        Assert::that($config)->isArray();

        $serviceIds = $config['external_event_consumers'] ?? [];

        return array_map(fn (string $id) => $container->get($id), $serviceIds);
    }
}
