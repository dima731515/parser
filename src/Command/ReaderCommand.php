<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'Reader',
    description: 'This command read site and save disk',
)]
class ReaderCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument('baseUrl', InputArgument::REQUIRED, 'Base URL to read')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
            ->addArgument('projectName', InputArgument::OPTIONAL, 'Project name. This name usually for save')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $baseUrl = $input->getArgument('baseUrl');

        if ($baseUrl) {
            $io->note(sprintf('You passed an argument: %s', $baseUrl));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
