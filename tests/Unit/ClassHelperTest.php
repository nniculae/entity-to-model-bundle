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

use Aristonet\EntityToModelBundle\Command\ClassHelper;
use PHPUnit\Framework\TestCase;

final class ClassHelperTest extends TestCase
{
    public function testGetShortClassName(): void
    {
        self::assertSame('App', ClassHelper::getShortClassName('App'));
        self::assertSame('DateTime', ClassHelper::getShortClassName(\DateTime::class));
        self::assertSame('Order', ClassHelper::getShortClassName('App\Order'));
        self::assertSame('Order', ClassHelper::getShortClassName('App\Entity\Order'));
    }

    public function testGetNamespace(): void
    {
        self::assertSame('', ClassHelper::getNamespace('App'));
        self::assertSame('', ClassHelper::getNamespace(\DateTime::class));
        self::assertSame('App', ClassHelper::getNamespace('App\Order'));
        self::assertSame('App\Entity', ClassHelper::getNamespace('App\Entity\Order'));
    }
}
