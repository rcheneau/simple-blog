<?php

namespace App\Tests\Controller;

use App\Repository\BlogPostRepository;
use App\Repository\ImageRepository;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
}
