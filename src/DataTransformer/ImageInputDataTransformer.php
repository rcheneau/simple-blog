<?php

declare(strict_types=1);

namespace App\DataTransformer;

use App\Entity\Image;
use App\Entity\User;
use App\Models\Input\ImageInput;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Uid\UuidV4;

final class ImageInputDataTransformer
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function createImage(ImageInput $input): Image
    {
        /** @var User $user */
        $user = $this->security->getUser();

        /** @var string $title */
        $title = $input->title;

        /** @var UploadedFile $file */
        $file = $input->file;

        return new Image(
            UuidV4::v4(),
            $user,
            new DateTimeImmutable(),
            $title,
            $input->description,
            $file,
        );
    }

    public function transforms(Image $image): ImageInput
    {
        /** @var ?UploadedFile $file */
        $file = $image->getFile();

        return new ImageInput(
            $image->getTitle(),
            $image->getDescription(),
            $file,
        );
    }
}
