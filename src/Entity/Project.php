<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{

    protected const DATA_PATH = 'data/projects';



    public function createFolder(): bool
    {
        try {
            $fileSystem = new Filesystem();
            $fileSystem->mkdir(Path::normalize(self::DATA_PATH .'/' . $this->path),);
        } catch (IOExceptionInterface $exception) {
            echo "An error occurred while creating your directory at ".$exception->getPath();
        }
        return true;
    }

    public function addPage(Page $page): bool
    {
        $pagePath = $page->getPath();
        $fileSystem = new Filesystem();
        $fileSystem->mkdir(Path::normalize(self::DATA_PATH .'/' . $pagePath));
        $fileSystem->touch(Path::normalize(self::DATA_PATH .'/' . $pagePath) . '/' .$page->toString());
        return true;
    }


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $path = null;

    #[ORM\Column(length: 255)]
    private ?string $base_url = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getBaseUrl(): ?string
    {
        return $this->base_url;
    }

    public function setBaseUrl(string $base_url): self
    {
        $this->base_url = $base_url;

        return $this;
    }
}
