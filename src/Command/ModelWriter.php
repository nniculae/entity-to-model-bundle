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

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;

class ModelWriter
{
    /**
     * @var array<int, class-string>
     */
    private array $entitiesClassNames;

    public function __construct(
        private readonly ConvertorInterface $convertor,
        private readonly string $modelDir,
        private readonly Filesystem $filesystem,
        readonly EntityManagerInterface $entityManager,
    ) {
        $metaDriverDataImpl = $entityManager->getConfiguration()->getMetadataDriverImpl();
        $this->entitiesClassNames = null === $metaDriverDataImpl ? [] : $metaDriverDataImpl->getAllClassNames();
    }

    public function writeAllModels(string $modelDirectory = null): string
    {
        $this->ensureEntitiesExists();
        $this->ensureDirExists();

        $modelDir = $this->determineModelDir($modelDirectory);

        foreach ($this->entitiesClassNames as $fullClassName) {
            $fileFullPath = $modelDir.'/'.mb_strtolower(ClassHelper::getShortClassName($fullClassName).'.ts');
            $this->filesystem->dumpFile($fileFullPath, $this->convertor->convert($fullClassName));
        }

        return $modelDir;
    }

    public function writeSingleModel(string $classShortName, string $modelDirectory = null): string
    {
        $this->ensureEntitiesExists();
        $this->ensureDirExists();

        $modelDir = $this->determineModelDir($modelDirectory);

        $namespace = ClassHelper::getNamespace($this->entitiesClassNames[0]);
        $fullClassName = $namespace.'\\'.$classShortName;
        if (!\in_array($fullClassName, $this->entitiesClassNames)) {
            throw new FileNotFoundException(sprintf('The entity %s was not found.', $fullClassName));
        }

        $fileFullPath = $modelDir.'/'.mb_strtolower($classShortName).'.ts';
        $this->filesystem->dumpFile($fileFullPath, $this->convertor->convert($fullClassName));

        return $modelDir;
    }

    private function ensureDirExists(): void
    {
        if (!$this->filesystem->exists($this->modelDir)) {
            $this->filesystem->mkdir($this->modelDir);
        }
    }

    private function determineModelDir(string $modelDirectory = null): string
    {
        if (null != $modelDirectory) {
            $modelDir = $modelDirectory;
            if (!$this->filesystem->exists($modelDir)) {
                throw new FileNotFoundException(sprintf('Directory %s was not found', $modelDir));
            }

            return $modelDir;
        } else {
            $modelDir = $this->modelDir;
            $this->ensureDirExists();

            return $modelDir;
        }
    }

    private function ensureEntitiesExists(): void
    {
        if (\count($this->entitiesClassNames) <= 0) {
            throw new FileNotFoundException('No entities found in this project');
        }
    }
}
