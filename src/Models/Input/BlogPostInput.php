<?php

declare(strict_types=1);

namespace App\Models\Input;

use App\Entity\BlogPost;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

final class BlogPostInput
{
    public function __construct(
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
        if ($this->title) {
            $blogPost->updateTitle($this->title);
        }

        if ($this->content) {
            $blogPost->updateContent($this->content);
        }
    }
}
