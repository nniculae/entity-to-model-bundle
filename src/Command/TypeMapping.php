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

namespace Aristonet\EntityToModelBundle\Command;

class TypeMapping
{
    public const PhpTs = [
        'bool' => 'boolean',
        'int' => 'number',
        'float' => 'number',
        'string' => 'string',
        'array' => 'any[]',
        'mixed' => 'any',
        'object' => 'object',
        'DateTime' => 'Date',
        'DateTimeImmutable' => 'Date',
        'DateInterval' => 'any',
    ];
}
