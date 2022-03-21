<?php

declare(strict_types=1);

namespace App\Cli;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;

final class ConsoleApplication extends Application
{
    public function __construct(
        private readonly ContainerInterface $container
    ) {
        parent::__construct('Bunchup', 'v1.0.0');

        $this->addCommands([
            $this->container->get(SignUpCommand::class),
            $this->container->get(ConsumeEventsCommand::class),
            $this->container->get(OutboxRelayCommand::class),
        ]);
    }
}
