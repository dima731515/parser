<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DomCrawler\Crawler;


#[AsCommand(
    name: 'Parser',
    description: 'Add a short description for your command',
)]
class ParserCommand extends Command
{
    protected Filesystem $fileSystem;
    protected Crawler $crawler;

    public function __construct(Filesystem $fileSystem)
    {
        parent::__construct();
        $this->fileSystem = $fileSystem;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'file')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $file = $input->getArgument('file');

        // php bin/console parser /Users/dima/Developer/parser/parser/data/projects/tbssssddindex
        $html = file_get_contents($file);
        $crawler = new Crawler();
        $crawler->addHtmlContent($html);
        //$nodeValues = $crawler->filter('a')->each(function (Crawler $node, $i) {
        //    return ['href' => $node->attr('href'), 'name' => $node->text()];
        //});
        $a = $crawler->filterXPath('//body/a');
        //$tags = $crawler->filterXPath('//body/*')->nodeName();
        //$nodeValues = $crawler->filter('a')->each(function (Crawler $node, $i) {
        //    return ['href' => $node->attr('href'), 'name' => $node->text()];
        //});

        echo '<pre>';
        print_r($a->getNode());
        echo '</pre>';
        die();
        foreach ($crawler as $domElement) {
            print_r($domElement->nodeName);
        }

        if ($file) {
            $io->note(sprintf('Parse file: %s', $file));
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
