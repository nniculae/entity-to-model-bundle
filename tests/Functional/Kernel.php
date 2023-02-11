<?php

declare(strict_types=1);

/*
 * This file is part of the Aristonet EntityToModelBundle package.
 *
 * c) Niculae Niculae
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Aristonet\EntityToModelBundle\Tests\Functional;

use Aristonet\EntityToModelBundle\AristonetEntityToModelBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    /**
     * Returns an array of bundles to register.
     *
     * @return iterable<mixed, BundleInterface>
     */
    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new DoctrineBundle(),
            new AristonetEntityToModelBundle(),
        ];
    }

    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader): void
    {
        $c->loadFromExtension('framework', [
            'secret' => 123,
            'router' => [
                'utf8' => true,
            ],
            'http_method_override' => false,
        ]);

        $dbal = [
            'driver' => 'pdo_sqlite',
            'url' => 'sqlite:///fake',
        ];

        $c->prependExtensionConfig('doctrine', [
            'dbal' => $dbal,
            'orm' => [
                'mappings' => [
                    'Aristonet\EntityToModelBundle\Tests' => [
                        'is_bundle' => false,
                        'dir' => '%kernel.project_dir%/tests/Entity',
                        'prefix' => 'Aristonet\EntityToModelBundle\Tests\Entity',
                        'alias' => 'Aristonet\EntityToModelBundle\Tests',
                    ],
                ],
            ],
        ]);
    }
}
