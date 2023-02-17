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

interface ModelWriterInterface
{
    public function writeAllModels(string $modelDirectory = null): string;

    public function writeSingleModel(string $classShortName, string $modelDirectory = null): string;
}
