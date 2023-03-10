<?php

namespace App\Entity;

use App\Repository\LinkRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LinkRepository::class)]
class Link
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $link = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isExec = null;

    #[ORM\Column(nullable: true)]
    private ?bool $is_exec = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function isIsExec(): ?bool
    {
        return $this->isExec;
    }

    public function setIsExec(?bool $isExec): self
    {
        $this->isExec = $isExec;

        return $this;
    }
}
