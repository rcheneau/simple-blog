<?php /** @noinspection PhpPropertyCanBeReadonlyInspection Because of doctrine proxy */

namespace App\Entity;

use App\Repository\ImageRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\UuidV4;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Use VichUploader to handle image upload.
 * Because of this most entity's attributes is set by a listener and cannot be initialized in controller.
 * These set methods (see Vich\UploadableField above file field for list) must also accept null on their setter
 * because Vich will call erase methods to set them at null temporarily on image file update.
 */
#[ORM\Entity(repositoryClass: ImageRepository::class)]
#[Vich\Uploadable]
class Image
{
    #[ORM\Column]
    private string $name = '';

    #[ORM\Column]
    private string $originalName = '';

    #[ORM\Column]
    private int $size = 0;

    #[ORM\Column]
    private string $mimeType = '';

    /** @var array<int, int> */
    #[ORM\Column]
    private array $dimensions = [];

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $updatedBy;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private DateTimeImmutable $updatedAt;

    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'uuid')]
        private UuidV4            $id,

        #[ORM\ManyToOne]
        #[ORM\JoinColumn(nullable: false)]
        private User              $createdBy,

        #[ORM\Column(type: 'datetime_immutable')]
        private DateTimeImmutable $createdAt,

        #[Groups(['search'])]
        #[ORM\Column(length: 25)]
        private string            $title,

        #[ORM\Column(length: 100, nullable: true)]
        private ?string           $description,

        #[Vich\UploadableField(
            mapping: 'image',
            fileNameProperty: 'name',
            size: 'size',
            mimeType: 'mimeType',
            originalName: 'originalName',
            dimensions: 'dimensions',
        )]
        private ?File             $file = null,
    )
    {
        if ($this->description === '') {
            $this->description = null;
        }
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(File $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name ?? '';

        return $this;
    }

    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    public function setOriginalName(?string $originalName): self
    {
        $this->originalName = $originalName ?? '';

        return $this;
    }

    public function getSize(): int
    {
        return $this->size;
    }


    public function setSize(?int $size): self
    {
        $this->size = $size ?? 0;

        return $this;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }


    public function setMimeType(?string $mimeType): self
    {
        $this->mimeType = $mimeType ?? '';

        return $this;
    }

    /**
     * @return int[]
     */
    public function getDimensions(): array
    {
        return $this->dimensions;
    }

    /**
     * @param int[] $dimensions
     * @return $this
     */
    public function setDimensions(?array $dimensions): self
    {
        $this->dimensions = $dimensions ?? [0, 0];

        return $this;
    }

    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function updatedByAt(User $user, DateTimeImmutable $dateTime): self
    {
        $this->updatedBy = $user;
        $this->updatedAt = $dateTime;

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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function updateTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description ?? '';
    }

    public function updateDescription(?string $description): self
    {
        $this->description = $description === '' ? null : $description;

        return $this;
    }

    public function getAlt(): string
    {
        return $this->description ?? $this->title;
    }
}
