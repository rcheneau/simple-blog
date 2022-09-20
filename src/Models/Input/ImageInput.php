<?php

declare(strict_types=1);

namespace App\Models\Input;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
final class ImageInput
{
    public ?string $name = null;

    public function __construct(
        #[NotBlank]
        #[Length(max: 25)]
        public ?string $title = null,

        #[Length(max: 100)]
        public ?string $description = null,

        #[File(maxSize: '4Mi', mimeTypes: ['image/png', 'image/jpeg'])]
        #[Vich\UploadableField(mapping: 'image', fileNameProperty: 'name')]
        public ?UploadedFile   $file = null,
    )
    {
    }
}
