<?php

namespace App\Tests\Controller;

use App\Repository\ImageRepository;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageControllerTest extends AbstractControllerTest
{
    public function testImageLarge()
    {
        $client = static::createClient();
        $this->login($client);

        $imageRepository = static::getContainer()->get(ImageRepository::class);
        $image = $imageRepository->findOneBy([]);

        $client->request('GET', '/images/large/' . $image->getId()->toRfc4122());

        $this->assertResponseIsSuccessful();
    }

    public function testImageOriginal()
    {
        $client = static::createClient();
        $this->login($client);

        $imageRepository = static::getContainer()->get(ImageRepository::class);
        $image = $imageRepository->findOneBy([]);

        $client->request('GET', '/images/original/' . $image->getId()->toRfc4122());

        $this->assertResponseIsSuccessful();
    }
}
