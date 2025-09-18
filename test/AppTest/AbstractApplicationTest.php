<?php

declare(strict_types=1);

namespace AppTest;

use App\ApplicationInterface;
use App\Clock;
use App\SchemaManager;
use App\TestClock;
use Assert\Assert;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

abstract class AbstractApplicationTest extends TestCase
{
    protected ApplicationInterface $application;

    private ContainerInterface $container;

    protected function setUp(): void
    {
        $_ENV['APPLICATION_ENV'] = 'application_testing';

        /** @var ContainerInterface $container */
        $this->container = require 'config/container.php';

        /** @var SchemaManager $schemaManager */
        $schemaManager = $container->get(SchemaManager::class);
        $schemaManager->updateSchema();
        $schemaManager->truncateTables();

        $this->application = $container->get(ApplicationInterface::class);
    }


    protected function nowIs(string $time): void
    {
        $testClock = $this->container->get(Clock::class);
        Assert::that($testClock)->isInstanceOf(TestClock::class);
        $testClock->setCurrentTime(new DateTimeImmutable($time));
    }
}
