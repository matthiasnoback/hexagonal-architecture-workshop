<?php

declare(strict_types=1);

namespace App\ExternalEvents;

use Assert\Assert;
use Psr\Container\ContainerInterface;
use TailEventStream\Consumer;
use TailEventStream\Producer;

final class EventStreamConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    public function getDependencies(): array
    {
        return [
            'factories' => [
                Consumer::class => fn (ContainerInterface $container) => new Consumer($this->getStreamFilePath(
                    $container
                )),
                Producer::class => fn (ContainerInterface $container) => new Producer($this->getStreamFilePath(
                    $container
                )),
            ],
        ];
    }

    private function getStreamFilePath(ContainerInterface $container): string
    {
        $config = $container->get('config');
        Assert::that($config)->isArray();

        $rootDir = $config['project_root_dir'];
        Assert::that($rootDir)->directory();

        $streamFilePath = $rootDir . '/var/stream-' . ($config['environment'] ?? 'development') . '.txt';

        Assert::that(dirname($streamFilePath))->directory();

        return $streamFilePath;
    }
}
