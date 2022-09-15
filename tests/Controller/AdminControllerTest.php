<?php

namespace App\Tests\Controller;

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
}
