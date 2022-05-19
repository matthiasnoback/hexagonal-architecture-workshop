<?php

declare(strict_types=1);

namespace App\Cli;

use App\ExternalEvents\ExternalEventConsumer;
use Assert\Assertion;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TailEventStream\Consumer;

final class ConsumeEventsCommand extends Command
{
    /**
     * @param array<ExternalEventConsumer> $externalEventConsumers
     */
    public function __construct(
        private readonly Consumer $consumer,
        private readonly array $externalEventConsumers,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('consume:events')
            ->addArgument('consumerServiceClass');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $externalEventConsumer = $this->resolveExternalEventConsumer($input);

        $externalEventConsumer->whenConsumerRestarted();

        $this->consumer->consume(
            function (string $eventName, array $eventData) use ($externalEventConsumer, $output) {
                $output->writeln('Consuming external event: ' . $eventName);

                $externalEventConsumer->whenExternalEventReceived($eventName, $eventData,);
            }
        );

        return 0;
    }

    private function resolveExternalEventConsumer(InputInterface $input): ExternalEventConsumer
    {
        $consumerServiceClass = $input->getArgument('consumerServiceClass');
        Assertion::string($consumerServiceClass);

        foreach ($this->externalEventConsumers as $eventConsumer) {
            if ($eventConsumer instanceof $consumerServiceClass) {
                return $eventConsumer;
            }
        }

        throw new RuntimeException(
            sprintf(
                'There is no external event consumer with class "%s", first add it to the list of consumers in ConfigProvider under "external_event_consumers"',
                $consumerServiceClass
            )
        );
    }
}
