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

use Aristonet\EntityToModelBundle\Command\ConvertorInterface;
use Aristonet\EntityToModelBundle\Command\ModelWriter;
use Aristonet\EntityToModelBundle\Command\ModelWriterInterface;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

final class ModelWriterTest extends TestCase
{
    private ModelWriterInterface $modelWriter;
    private MockObject $convertor;
    private MockObject $filesystem;

    protected function setUp(): void
    {
        $this->convertor = self::createMock(ConvertorInterface::class);
        $this->convertor->method('convert')->willReturn('model-ts');
        $this->filesystem = self::createMock(Filesystem::class);
        $mappingDriver = self::createStub(MappingDriver::class);
        $mappingDriver->method('getAllClassNames')->willReturn([
            'App\Entity\Order',
            'App\Entity\OrderItem',
        ]);
        $configuration = self::createStub(Configuration::class);
        $configuration->method('getMetadataDriverImpl')->willReturn($mappingDriver);
        $entityManager = self::createStub(EntityManagerInterface::class);
        $entityManager->method('getConfiguration')->willReturn($configuration);
        $modelDir = 'path';

        $this->modelWriter = new ModelWriter($this->convertor, $modelDir, $this->filesystem, $entityManager);
    }

    public function testWriteAllModels(): void
    {
        $this->filesystem->expects(self::exactly(2))
            ->method('dumpFile')
            ->with(
                self::callback(static function (string $filePath) {
                    static $count = 0;
                    $args = ['path/order.ts', 'path/orderitem.ts'];
                    self::assertSame($args[$count], $filePath);
                    ++$count;

                    return true;
                })
            );

        $this->convertor->expects(self::exactly(2))
            ->method('convert')
            ->with(self::callback(static function (string $classFullName): bool {
                static $count = 0;
                $args = ['App\Entity\Order', 'App\Entity\OrderItem'];
                self::assertSame($args[$count], $classFullName);
                ++$count;

                return true;
            }));

        $this->modelWriter->writeAllModels();
    }

    public function testWriteSingleModel(): void
    {
        $this->filesystem->expects(self::once())
            ->method('dumpFile')
            ->with('path/order.ts');

        $this->convertor->expects(self::once())
            ->method('convert')
            ->with('App\Entity\Order');

        $this->modelWriter->writeSingleModel('Order');
    }

    public function testWriteSingleModelThrows(): void
    {
        self::expectException(\InvalidArgumentException::class);
        $this->modelWriter->writeSingleModel('NoEntity');
    }
}
