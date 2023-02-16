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

namespace Aristonet\EntityToModelBundle\Tests\Unit;

use Aristonet\EntityToModelBundle\Command\ConvertEntityToModelCommand;
use Aristonet\EntityToModelBundle\Command\ModelWriterInterface;
use Aristonet\EntityToModelBundle\Exception\NoEntitiesFoundException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class ConvertEntityToModelCommandTest extends TestCase
{
    public function testExecuteShouldWriteAllModels(): void
    {
        $commandTester = $this->createCommandTester('writeAllModels', 'default\model');
        self::assertSame(Command::SUCCESS, $commandTester->execute([]));
        self::assertStringContainsString('The model(s) have been generated in directory default\model', $commandTester->getDisplay());
    }

    public function testExecuteShouldWriteSingleModel(): void
    {
        $commandTester = $this->createCommandTester('writeSingleModel', 'default\model');
        self::assertSame(Command::SUCCESS, $commandTester->execute([
            '--className' => 'Order',
        ]));
        self::assertStringContainsString('The model(s) have been generated in directory default\model', $commandTester->getDisplay());
    }

    public function testExecuteShouldFail(): void
    {
        $commandTester = $this->createCommandTester('writeAllModels', new NoEntitiesFoundException('No entities found'), true);
        self::assertSame(Command::FAILURE, $commandTester->execute([]));
        self::assertStringContainsString('No entities found', $commandTester->getDisplay());
    }

    private function createCommandTester(string $method, mixed $return, bool $throws = false): CommandTester
    {
        $modelWriter = self::createStub(ModelWriterInterface::class);
        if (true === $throws) {
            $modelWriter->method($method)->willThrowException($return);
        } else {
            $modelWriter->method($method)->willReturn($return);
        }
        $convertEntityToModelCommand = new ConvertEntityToModelCommand($modelWriter);
        $application = new Application();
        $application->add($convertEntityToModelCommand);
        $command = $application->find('convert:entitytomodel');

        return new CommandTester($command);
    }
}
