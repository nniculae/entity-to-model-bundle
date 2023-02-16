<?php

/*
 * This file is part of the Aristonet EntityToModelBundle package.
 *
 * @author Niculae Niculae
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Aristonet\EntityToModelBundle\Tests\Unit;

use Aristonet\EntityToModelBundle\Command\Convertor;
use Aristonet\EntityToModelBundle\Tests\Entity\OrderItem;
use Aristonet\EntityToModelBundle\Tests\Functional\Kernel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\PropertyInfo\DoctrineExtractor;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;

class ConvertorTest extends KernelTestCase
{
    public function testConvert(): void
    {
        static::bootKernel();
        /** @var EntityManagerInterface entityManager */
        $entityManager = static::$kernel->getContainer()->get('doctrine.orm.entity_manager');
        // a full list of extractors is shown further below
        $doctrineExtractor = new DoctrineExtractor($entityManager);
        // list of PropertyListExtractorInterface (any iterable)
        $listExtractors = [$doctrineExtractor];
        // list of PropertyTypeExtractorInterface (any iterable)
        $typeExtractors = [$doctrineExtractor];
        // list of PropertyAccessExtractorInterface (any iterable)
        $accessExtractors = [$doctrineExtractor];

        $propertyInfo = new PropertyInfoExtractor(
            $listExtractors,
            $typeExtractors,
            [],
            $accessExtractors,
            []
        );

        $convertor = new Convertor($propertyInfo);

        $tsModel = "import { Order } from  './order';\n\n".
            "export class OrderItem{\n".
            "\tpublic id: number;\n".
            "\tpublic name: string;\n".
            "\tpublic parentOrder: Order;\n".
            '}';
        self::assertSame($tsModel, $convertor->convert(OrderItem::class));
    }

    protected static function getKernelClass(): string
    {
        return Kernel::class;
    }
}
