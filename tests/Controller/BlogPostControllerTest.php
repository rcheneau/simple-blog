<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BlogPostControllerTest extends WebTestCase
{
    public function testBlogPostList()
    {
        $client = static::createClient();
        $client->request('GET', '/fr/blog');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Articles');

        $client->request('GET', '/en/blog');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Posts');

        $client->request('GET', '/en/blog/1');
        $this->assertResponseIsSuccessful();

        $client->request('GET', '/en/blog/2');
        $this->assertResponseIsSuccessful();
    }

    public function testBlogPostItem()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/fr/blog');

        $this->assertResponseIsSuccessful();
        $link = $crawler->filter('.card-footer.text-center a')->link();

        $crawler = $client->request('GET', $link->getUri());
        $this->assertResponseIsSuccessful();
    }
}