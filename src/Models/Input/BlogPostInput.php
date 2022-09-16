<?php

declare(strict_types=1);

namespace App\Models\Input;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

final class BlogPostInput
{
    public function __construct(
        #[NotBlank]
        #[Length(max: 255)]
        public string $title = '',

        #[NotBlank]
        public string $content = '',
    )
    {
    }
}
