<?php

/** @noinspection PhpPropertyCanBeReadonlyInspection (because of doctrine proxy) */

namespace App\Entity;

use App\Repository\BlogPostRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV4;

#[ORM\Entity(repositoryClass: BlogPostRepository::class)]
class BlogPost
{
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $updatedBy = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'uuid')]
        private UuidV4            $id,

        #[ORM\Column]
        private string            $title,

        #[ORM\Column(unique: true)]
        private string            $slug,

        #[ORM\Column(type: Types::TEXT)]
        private string            $content,

        #[ORM\ManyToOne]
        #[ORM\JoinColumn(nullable: false)]
        private User              $author,

        #[ORM\Column]
        private DateTimeImmutable $createdAt,

        #[ORM\ManyToOne]
        #[ORM\JoinColumn(nullable: true)]
        private ?Image            $image = null,
    )
    {
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /** @noinspection PhpUnused */
    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    /** @noinspection PhpUnused */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updateTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function updateContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function updatedByAt(User $user, DateTimeImmutable $date): self
    {
        $this->updatedBy = $user;
        $this->updatedAt = $date;

        return $this;
    }

    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function updateImage(?Image $image): self
    {
        $this->image = $image;

        return $this;
    }
}
