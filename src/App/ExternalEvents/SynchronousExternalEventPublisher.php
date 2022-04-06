<?php
declare(strict_types=1);

namespace App\ExternalEvents;

use Assert\Assertion;
use Psr\Container\ContainerInterface;

final class SynchronousExternalEventPublisher implements ExternalEventPublisher
{
    /**
     * @param array<string> $serviceIds
     */
    public function __construct(
        private ContainerInterface $container,
        private array $serviceIds,
    ) {
    }

    public function publish(string $eventType, array $eventData): void
    {
        foreach ($this->externalEventConsumers() as $eventConsumer) {
            $eventConsumer->whenExternalEventReceived(
                $eventType,
                $eventData,
            );
        }
    }

    /**
     * @return array<ExternalEventConsumer>
     */
    private function externalEventConsumers(): array
    {
        $services = array_map(fn (string $id) => $this->container->get($id), $this->serviceIds);

        Assertion::allIsInstanceOf($services, ExternalEventConsumer::class);

        return $services;
    }
}
