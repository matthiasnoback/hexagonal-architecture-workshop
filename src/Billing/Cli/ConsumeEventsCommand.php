<?php
declare(strict_types=1);

namespace Billing\Cli;

use App\ConsumerRestarted;
use App\EventDispatcher;
use App\ExternalEventReceived;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TailEventStream\Consumer;

final class ConsumeEventsCommand extends Command
{
    public function __construct(
        private readonly Consumer $consumer,
        private readonly EventDispatcher $eventDispatcher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('consume:events');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->eventDispatcher->dispatch(new ConsumerRestarted());

        $this->consumer->consume(
            function (string $eventName, array $eventData) use ($output) {
                $output->writeln('Consuming external event: ' . $eventName);
                $this->eventDispatcher->dispatch(
                    new ExternalEventReceived($eventName, $eventData)
                );
            }
        );

        return 0;
    }
}
