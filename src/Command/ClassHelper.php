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

class ClassHelper
{
    public static function getShortClassName(string $fullClassName): string
    {
        $pos = strrpos($fullClassName, '\\');

        if (false === $pos) {
            return $fullClassName;
        }

        return substr($fullClassName, $pos + 1);
    }

    public static function getNamespace(string $fullClassName): string
    {
        return substr($fullClassName, 0, strrpos($fullClassName, '\\') ?: 0);
    }
}
