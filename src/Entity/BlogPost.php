<?php

namespace App\Entity;

use App\Repository\BlogPostRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV4;

#[ORM\Entity(repositoryClass: BlogPostRepository::class)]
class BlogPost
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'uuid')]
        private UuidV4             $id,

        #[ORM\Column(type: Types::TEXT)]
        private string             $content,

        #[ORM\ManyToOne]
        #[ORM\JoinColumn(nullable: false)]
        private User               $author,

        #[ORM\Column]
        private DateTimeImmutable $createdAt,
    )
    {
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
