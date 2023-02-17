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

use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Symfony\Component\PropertyInfo\Type;

class Convertor implements ConvertorInterface
{
    public function __construct(
        private readonly PropertyInfoExtractorInterface $propertyInfoExtractor,
    ) {
    }

    private string $imports = '';

    public function convert(string $fullClassName): string
    {
        $this->imports = '';

        $interfaceDefinition = 'export class ' . ClassHelper::getShortClassName($fullClassName) . '{' . \PHP_EOL;
        $typescriptProp = '';
        $properties = $this->propertyInfoExtractor->getProperties($fullClassName);
        if (null === $properties) {
            return '';
        }

        foreach ($properties as $prop) {
            /** @var Type[] $types */
            $types = $this->propertyInfoExtractor->getTypes($fullClassName, $prop) ?: [];

            if (\count($types) > 0) {
                $type = $types[0];
                $nullableOp = $type->isNullable() ? '?' : '';

                $className = $type->getClassName();

                if (null === $className) {
                    $typeTs = TypeMapping::PhpTs[$type->getBuiltinType()] ?? 'any';
                    $typescriptProp .= sprintf("\tpublic %s%s: %s;%s", $prop, $nullableOp, $typeTs, \PHP_EOL);
                } elseif (false === $type->isCollection()) {
                    $typeTs = TypeMapping::PhpTs[$className] ?? null;
                    if (null === $typeTs) {
                        $typeTs = ClassHelper::getShortClassName($className);
                        $this->handleImports($typeTs);
                    }

                    $typescriptProp .= sprintf("\tpublic %s%s: %s;%s", $prop, $nullableOp, $typeTs, \PHP_EOL);
                } else {
                    $collectionValueType = $type->getCollectionValueTypes()[0];

                    $className = $collectionValueType->getClassName();
                    if (null === $className) {
                        continue;
                    }
                    $typeTs = ClassHelper::getShortClassName($className);
                    $this->handleImports($typeTs);

                    $typescriptProp .= sprintf("\tpublic %s%s: %s[];%s", $prop, $nullableOp, $typeTs, \PHP_EOL);
                }
            }
        }
        $interfaceDefinition .= $typescriptProp;
        $interfaceDefinition .= '}';

        $this->imports .= '' === $this->imports ? '' : \PHP_EOL;
        $this->imports .= $interfaceDefinition;

        return $this->imports;
    }

    private function handleImports(string $typeTs): void
    {
        $fileName = mb_strtolower($typeTs);
        if (false === mb_strpos($this->imports, $fileName)) {
            $this->imports .= sprintf('import { %s } from  \'./%s\';%s', $typeTs, $fileName, \PHP_EOL);
        }
    }
}
