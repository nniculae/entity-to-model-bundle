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

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'convert:entitytomodel',
    description: 'It converts doctrine entities to typescript models',
)]
class ConvertEntityToModelCommand extends Command
{
    public function __construct(
        private readonly ModelWriter $modelWriter,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('className', null, InputOption::VALUE_OPTIONAL, 'Entity short class name.')
            ->addOption('modelDir', null, InputOption::VALUE_OPTIONAL, 'The full path of de models directory');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            if (null === $input->getOption('className')) {
                $path = $this->modelWriter->writeAllModels((string) $input->getOption('modelDir'));
            } else {
                $path = $this->modelWriter->writeSingleModel(trim((string) $input->getOption('className')), (string) $input->getOption('modelDir'));
            }
        } catch (\Exception $exc) {
            $io->error($exc->getMessage());

            return Command::FAILURE;
        }

        $io->success(sprintf('The model(s) have been generated in directory %s.', $path));

        return Command::SUCCESS;
    }
}
