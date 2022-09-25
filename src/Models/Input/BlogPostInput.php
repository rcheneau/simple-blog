<?php

declare(strict_types=1);

namespace App\Models\Input;

use App\Entity\BlogPost;
use App\Entity\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

final class BlogPostInput
{
    public function __construct(
        public ?Image $image = null,

        #[NotBlank]
        #[Length(max: 255)]
        public ?string $title = null,

        #[NotBlank]
        public ?string $content = null,
    )
    {
    }

    public function updateBlogPost(BlogPost $blogPost): void
    {
        if ($this->image) {
            $blogPost->updateImage($this->image);
        }

        if ($this->title) {
            $blogPost->updateTitle($this->title);
        }

        if ($this->content) {
            $blogPost->updateContent($this->content);
        }
    }
}
