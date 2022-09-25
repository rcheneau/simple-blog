<?php

declare(strict_types=1);

namespace App\DataTransformer;

use App\Entity\BlogPost;
use App\Entity\User;
use App\Models\Input\BlogPostInput;
use App\Service\UniqueSlugger;
use DateTimeImmutable;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Uid\UuidV4;

final class BlogPostInputDataTransformer
{
    private UniqueSlugger $slugger;
    private Security $security;

    public function __construct(UniqueSlugger $slugger, Security $security)
    {
        $this->slugger = $slugger;
        $this->security = $security;
    }

    public function createBlogPost(BlogPostInput $input): BlogPost
    {
        /** @var User $user */
        $user = $this->security->getUser();

        return new BlogPost(
            UuidV4::v4(),
            $input->title ?? '',
            $this->slugger->uniqueSlugInEntity($input->title ?? '', BlogPost::class)->toString(),
            $input->content ?? '',
            $user,
            new DateTimeImmutable(),
            $input->image
        );
    }

    public function transforms(BlogPost $blogPost): BlogPostInput
    {
        return new BlogPostInput(
            $blogPost->getImage(),
            $blogPost->getTitle(),
            $blogPost->getContent(),
        );
    }
}
