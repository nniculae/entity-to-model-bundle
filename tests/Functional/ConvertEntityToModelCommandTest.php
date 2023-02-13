<?php

declare(strict_types=1);

/*
 * This file is part of the Aristonet EntityToModelBundle package.
 *
 * @author Niculae Niculae
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Aristonet\EntityToModelBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class ConvertEntityToModelCommandTest extends KernelTestCase
{
    public function testExecuteWithDefaultOptions(): void
    {
        $kernel = self::bootKernel([
            'environment' => 'test',
            'debug' => false,
        ]);

        $application = new Application($kernel);
        $command = $application->find('convert:entitytomodel');

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $generatedModelsDir = $kernel->getProjectDir().'/model';
        $finderGeneratedFiles = new Finder();
        $finderGeneratedFiles->files()->in($generatedModelsDir)->sortByName();
        $generatedFiles = iterator_to_array($finderGeneratedFiles, false);

        $fixturesDir = $kernel->getProjectDir().'/tests/model';
        $finderFixtures = new Finder();
        $finderFixtures->files()->in($fixturesDir)->sortByName();
        $fixtures = iterator_to_array($finderGeneratedFiles, false);

        $this->assertSame(\count($generatedFiles), \count($fixtures));

        for ($i = 0; $i < \count($generatedFiles); ++$i) {
            $this->assertSame($generatedFiles[$i]->getContents(), $fixtures[$i]->getContents());
        }

        $fileSystem = new Filesystem();
        $fileSystem->remove($generatedModelsDir);

        $commandTester->assertCommandIsSuccessful();
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('model', $output);
    }

    public function testExecuteWithOptionClassName(): void
    {
        $kernel = self::bootKernel([
            'environment' => 'test',
            'debug' => false,
        ]);

        $application = new Application($kernel);
        $command = $application->find('convert:entitytomodel');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--className' => 'Order',
        ]);

        $orderModel = $kernel->getProjectDir().'/model/order.ts';
        $orderFixture = $kernel->getProjectDir().'/tests/model/order.ts';

        $this->assertSame(file_get_contents($orderModel), file_get_contents($orderFixture));

        $fileSystem = new Filesystem();
        $fileSystem->remove($kernel->getProjectDir().'/model');

        $commandTester->assertCommandIsSuccessful();
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('model', $output);
    }

    protected static function getKernelClass(): string
    {
        return Kernel::class;
    }
}
