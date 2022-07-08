<?php

declare(strict_types=1);

namespace AppTest;

use App\Cli\ConsoleApplication;
use App\SchemaManager;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Tester\ApplicationTester;

final class SignUpCommandTest extends TestCase
{
    public function testItSchedulesAMeetup(): void
    {
        $projectRootDir = __DIR__ . '/../../';

        $_ENV['APPLICATION_ENV'] = 'end_to_end_testing';

        $_SERVER['HTTP_X_CURRENT_TIME'] = 'now';

        /** @var ContainerInterface $container */
        $container = require 'config/container.php';

        /** @var SchemaManager $schemaManager */
        $schemaManager = $container->get(SchemaManager::class);
        $schemaManager->updateSchema();
        $schemaManager->truncateTables();

        $application = $container->get(ConsoleApplication::class);
        $application->setAutoExit(false);
        $application->setCatchExceptions(false);

        $applicationTester = new ApplicationTester($application);

        $exitCode = $applicationTester->run(
            [
                'command' => 'sign-up',
                'name' => 'Regular user',
                'emailAddress' => 'user@gmail.com',
                'userType' => 'RegularUser',
            ]
        );

        self::assertSame(0, $exitCode);

        $this->assertStringContainsString('User was signed up successfully', $applicationTester->getDisplay());
    }
}
