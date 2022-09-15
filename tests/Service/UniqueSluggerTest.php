<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\BlogPost;
use App\Repository\UserRepository;
use App\Service\UniqueSlugger;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Uid\UuidV4;

final class UniqueSluggerTest extends KernelTestCase
{
    use ReloadDatabaseTrait;

    public function testUniqueSlugger()
    {
        self::bootKernel();
        $container      = UniqueSluggerTest::getContainer();
        $slugger        = $container->get(SluggerInterface::class);
        $em             = $container->get(EntityManagerInterface::class);
        $userRepository = $container->get(UserRepository::class);

        $uniqueSlugger = new UniqueSlugger($slugger, $em);

        $post = $this->createCreateBlogPost('My unique title', $uniqueSlugger, $em, $userRepository);
        $this->assertEquals($slugger->slug('My unique title'), $post->getSlug());

        $post = $this->createCreateBlogPost('My unique title', $uniqueSlugger, $em, $userRepository);
        $this->assertEquals($slugger->slug('My unique title')->append('-1')->toString(), $post->getSlug());

        $post = $this->createCreateBlogPost('My unique title', $uniqueSlugger, $em, $userRepository);
        $this->assertEquals($slugger->slug('My unique title')->append('-2')->toString(), $post->getSlug());

        $post = $this->createCreateBlogPost($slugger->slug('My unique title')->toString(), $uniqueSlugger, $em, $userRepository);
        $this->assertEquals($slugger->slug('My unique title')->append('-3')->toString(), $post->getSlug());

        $post = $this->createCreateBlogPost($slugger->slug('My unique title 3')->toString(), $uniqueSlugger, $em, $userRepository);
        $this->assertEquals($slugger->slug('My unique title')->append('-3-4')->toString(), $post->getSlug());

        $post = $this->createCreateBlogPost('My unique title', $uniqueSlugger, $em, $userRepository);
        $this->assertEquals($slugger->slug('My unique title')->append('-5'), $post->getSlug());

        $post = $this->createCreateBlogPost($slugger->slug('My unique title 3')->toString(), $uniqueSlugger, $em, $userRepository);
        $this->assertEquals($slugger->slug('My unique title')->append('-3-5')->toString(), $post->getSlug());

        $post = $this->createCreateBlogPost($slugger->slug('My unique title 3 5')->toString(), $uniqueSlugger, $em, $userRepository);
        $this->assertEquals($slugger->slug('My unique title')->append('-3-5-6')->toString(), $post->getSlug());
    }

    private function createCreateBlogPost(string                 $title,
                                          UniqueSlugger          $uniqueSlugger,
                                          EntityManagerInterface $em,
                                          UserRepository         $userRepository): BlogPost
    {
        $user = $userRepository->findOneBy([]);
        $slug = $uniqueSlugger->uniqueSlugInEntity($title, BlogPost::class);

        $post = new BlogPost(
            UuidV4::v4(), $title, $slug->toString(), 'My content...', $user, new DateTimeImmutable()
        );

        $em->persist($post);
        $em->flush();

        return $post;
    }
}
