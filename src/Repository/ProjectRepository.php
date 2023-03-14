<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @extends ServiceEntityRepository<Project>
 *
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    protected Project $project;
    protected HttpClientInterface $client;
    protected Filesystem $filesystem;

    public function __construct(ManagerRegistry $registry, Filesystem $filesystem, HttpClientInterface $client)
    {
        parent::__construct($registry, Project::class);

        $this->filesystem = $filesystem;
        $this->client     = $client;
    }

    public function init(string $url, string $projectName = ''): Project
    {
        $url   = parse_url($url);
        $host  = str_replace('www.', '', $url['host']);
        $projectName = $projectName ?? str_replace('.', '_', $host);

        $project = $this->findOneBy(['title' => $projectName]);

        if(!$project){
            $project = new Project();
            $project->setTitle($projectName);
            $project->setPath($projectName);
            $project->setBaseUrl($host);
            $this->save($project);
        }
        $this->project = $project;

        $this->createProjectFolder($project);
        $this->requestIndex();
        return $project;
    }

    protected function createProjectFolder(Project $project): bool
    {
        try{
            $this->filesystem->mkdir('data/projects/' . Path::normalize($project->getPath()));
            return true;
        } catch (IOExceptionInterface $exception) {
            echo "An error occurred while creating your directory at " . $exception->getPath();
        }
    }

    public function requestIndex(): bool
    {
        $this->request($this->project->getBaseUrl());
    }

    public function requestNext(): bool
    {

    }

    protected function request(string $url): bool
    {
        // Запрос
        $url   = 'https://' . $url;
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
        $arUrl = parse_url($url);

        $filePath = 'data/projects/' . $this->project->getPath();;
        $filePath .= $arUrl['path']  ?? 'index';
        $filePath .= $arUrl['query'] ?? '';

        $this->filesystem->touch(Path::normalize($filePath));
        $this->filesystem->appendToFile(Path::normalize($filePath), $pageContent);

        echo '<pre>';
        echo 'test';
        echo '</pre>';
        die();
    }

    public function save(Project $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Project $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }



//    /**
//     * @return Project[] Returns an array of Project objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    public function findOneBySomeField($value): ?Project
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
