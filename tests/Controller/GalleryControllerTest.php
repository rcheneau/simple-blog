<?php

namespace App\Tests\Controller;

use App\Repository\ImageRepository;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class GalleryControllerTest extends AbstractControllerTest
{
    public function testGallery()
    {
        $client = static::createClient();
        $this->login($client);

        $client->request('GET', '/fr/gallery');

        $this->assertResponseStatusCodeSame(200);
    }
}
