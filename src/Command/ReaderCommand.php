<?php

namespace App\Command;

use App\Entity\Page;
use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\HttpClient\HttpClientInterface;


#[AsCommand(
    name: 'Reader',
    description: 'This command read site and save disk',
)]
class ReaderCommand extends Command
{
    protected EntityManagerInterface $entityManager;
    protected Filesystem $fileSystem;
    protected HttpClientInterface $client;

    public function __construct(EntityManagerInterface $entityManager, Filesystem $fileSystem, HttpClientInterface $client)
    {
       parent::__construct();
       $this->entityManager = $entityManager;
       $this->fileSystem = $fileSystem;
       $this->client = $client;
    }
    protected function configure(): void
    {
        $this
            ->addArgument('baseUrl', InputArgument::REQUIRED, 'Base URL to read')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
            ->addArgument('projectName', InputArgument::OPTIONAL, 'Project name. This name usually for save')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     *
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $baseUrl     = $input->getArgument('baseUrl');
        $projectName = $input->getArgument('projectName');

        if ($input->getOption('option1')) {
            // ...
        }

        if (!$baseUrl) {
            throw new \Exception('Invalid Argument');
        }

        $url = parse_url($baseUrl);
        if(!$url['host']) throw new \Exception('Argument invalid');

        $host  = str_replace('www.', '', $url['host']);
        $title = str_replace('.', '_', $host);

        $repository = $this->entityManager->getRepository(Project::class);

        $project = $repository->findOneBy([
            'title'    => $projectName ?? $host,
            'base_url' => $host
        ]);

        if(!$project){
            $project = new Project();
            $project->setBaseUrl($host);
            $project->setTitle($projectName ?? $host);
            $project->setPath($projectName ?? $title);
            $this->entityManager->persist($project);
            $project = $this->entityManager->flush();
        }
        // Каталог проекта
        $project->createFolder();

        // Запрос
        $url   = 'https://' . $host;
        $arUrl = parse_url($url);
        $response = $this->client->request(
            'GET',
            $url
        );

        if(200 !== $response->getStatusCode()){
            throw new \Exception('что-то пошло не так');
        }
        $headers = $response->getHeaders();
        foreach($headers as $key => $value){
            if('content-type' === $key){
                // content type array
            }
            if('set-coolie' === $key){
                //
            }
        }
        $pageContent = $response->getContent();
        $path = 'project/tbs/' . 'index';

        $page = new Page();
        $page->setTitle('test');
        $page->setPath($path);

        $this->entityManager->persist($project);
        $page = $this->entityManager->flush();
        $page->saveFile($pageContent);

        echo '<pre>';
        print_r($response->getStatusCode());
        print_r($response->getHeaders());
        echo '</pre>';



        // проверить нет ли такого проекта в базе
            // если нет, создать
            // пометить как первый запуск? isFirst
        // получить не обработанные ссылки из базы
            // table: links
                // link, create_at, download_at, parse_at
        // если ссылки нет, формируем ссылку по умолчанию ( base_url: https://test.ru)
        // new ProjectRepository()
        // project->requestIndex()
        // project->requestNextLink()
            // project->setPage();
            // project->getNotParsePage(); // получить не парсенную страницу

        // crawler = new Crawler(page)
        // crawler->configParseLink(function(link) use project{
        //    project->setLink(link);
        //});
        // crawler->configParseImg(function(img) use project{
        //    project->setImage(link);
        //});
        // crawler->run();


        // ======= Инициализация ==========
        // ProjectInitCommand
        // создать проект
            // создать запись о проекте
            // title, path, base_url, create_at, update_at, last_request_at, is_done
            // сделать запрос главной страницы
        // сделать запрос ссылок (с пагинацией)
        // =================


        // ======= Запрос страницы ==========
        // $requestUrl = baseUrl . $pagePath ?? '';
        // запрашиваем страницу
        // cохранить страницу в проекте
            // title, path, create_at, update_at,
        // =================


        // ======== парсер страницы =========
        // PageParseCommand projectName pageCode
        // получить ссылки страницы
        // получить ссылки на картинки страницы
        // title, code, path, create_at, update_at, parse_at, link_id
        // =================


        // ======== скачивание картинок ========
        // не скачивать большие
        // не скачивать картинки интерфейса
        // =================





        echo '<pre>';
        print_r($project);
        echo '</pre>';
        die();


        $io->note(sprintf('You passed an argument: %s', $baseUrl));


        $projectName = '';

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
