<?php

declare(strict_types=1);

namespace AppTest;

use App\Cli\ConsoleApplication;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Component\Filesystem\Filesystem;

final class SignUpCommandTest extends TestCase
{
    public function testItSchedulesAMeetup(): void
    {
        $projectRootDir = __DIR__ . '/../../';

        $_ENV['APPLICATION_ENV'] = 'end_to_end_testing';

        $filesystem = new Filesystem();
        $filesystem->remove($projectRootDir . '/var/app-end_to_end_testing.sqlite');

        /** @var ContainerInterface $container */
        $container = require $projectRootDir . '/config/container.php';

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
