<?php

namespace App\Tests\Controller;

use App\Repository\BlogPostRepository;

class AdminControllerTest extends AbstractControllerTest
{
    public function testAdminHomepage()
    {
        $client = static::createClient();
        $this->login($client);
        $client->request('GET', '/fr/admin');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Administration');
    }

    public function testAdminBlogPostManage()
    {
        $client = static::createClient();
        $this->login($client);
        $client->request('GET', '/fr/admin/posts');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Gestion des articles');
    }

    public function testAdminCreateBlogPost()
    {
        $client = static::createClient();

        /** @var BlogPostRepository $blogPostRepository */
        $blogPostRepository = static::getContainer()->get(BlogPostRepository::class);
        $count =  $blogPostRepository->count([]);

        $this->login($client);

        $crawler = $client->request('GET', '/fr/admin/posts/create');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Créer un article');

        $form = $crawler->filter('form')->form();
        $form['blog_post[title]'] = 'Test title';
        $form['blog_post[content]'] = 'Test content...';
        $client->submit($form);
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Gestion des articles');

        $this->assertEquals($count + 1, $blogPostRepository->count([]));
    }

    public function testAdminUpdateBlogPost()
    {
        $client = static::createClient();

        /** @var BlogPostRepository $blogPostRepository */
        $blogPostRepository = static::getContainer()->get(BlogPostRepository::class);
        $post =  $blogPostRepository->findOneBy([]);

        $this->login($client);

        $crawler = $client->request('GET', '/fr/admin/posts/edit/' . $post->getSlug());
        $this->assertResponseIsSuccessful();
        $this->assertInputValueSame('blog_post[title]', $post->getTitle());
        $this->assertSelectorTextSame('textarea[name="blog_post[content]"]', $post->getContent());

        $form = $crawler->filter('form')->form();
        $form['blog_post[title]'] = 'Updated title';
        $form['blog_post[content]'] = 'Updated content';
        $client->submit($form);

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Gestion des articles');

        $updatedPost =  $blogPostRepository->find($post->getId());
        $this->assertEquals('Updated title',$updatedPost->getTitle());
        $this->assertEquals('Updated content',$updatedPost->getContent());
    }
}
