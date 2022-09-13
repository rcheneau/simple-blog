<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends AbstractControllerTest
{
    public function testAdminBlogPostManage()
    {
        $client = static::createClient();
        $this->login($client);
        $client->request('GET', '/fr/admin/posts');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Gestion des articles');
    }
}