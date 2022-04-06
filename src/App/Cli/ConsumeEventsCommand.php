<?php
declare(strict_types=1);

namespace App\Cli;

use App\ExternalEvents\ExternalEventConsumer;
use App\ExternalEvents\ExternalEventReceived;
use Assert\Assertion;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TailEventStream\Consumer;

final class ConsumeEventsCommand extends Command
{
    public function __construct(
        private readonly Consumer $consumer,
        private readonly ContainerInterface $container,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('consume:events')
            ->addArgument('consumerServiceId');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $externalEventConsumer = $this->resolveExternalEventConsumer($input);

        $externalEventConsumer->whenConsumerRestarted();

        $this->consumer->consume(
            function (string $eventName, array $eventData) use ($externalEventConsumer, $output) {
                $output->writeln('Consuming external event: ' . $eventName);

                $externalEventConsumer->whenExternalEventReceived(
                    new ExternalEventReceived($eventName, $eventData)
                );
            }
        );

        return 0;
    }

    private function resolveExternalEventConsumer(InputInterface $input): ExternalEventConsumer
    {
        $consumerServiceId = $input->getArgument('consumerServiceId');
        Assertion::string($consumerServiceId);

        $externalEventConsumer = $this->container->get($consumerServiceId);
        Assertion::isInstanceOf($externalEventConsumer, ExternalEventConsumer::class);

        return $externalEventConsumer;
    }
}
